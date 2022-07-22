<?php

namespace App\Controller\Trait;
use App\Controller\Trait\DateTrait;
use Symfony\Component\Process\Process;

/**
 * Trait WhoisTrait.
 */
trait WhoisTrait
{
    use DateTrait;
    protected function whois(String $name)
    {
        $process = new Process(["whois $name"]);
        $process->start();
        $whois = $process->getOutput();
        //$whois = shell_exec("whois $name");
        $domainStatus = $this->getStatusFromWhois($whois, $name);
        $hold = $this->get_string_between($whois,"hold:", "\n");
        $hold = trim($hold) === 'YES';
        $lastUpdateDate = $this->get_string_between($whois,"last-update:", "\n");
        $lastUpdateDateFormatted = new \DateTime(date("Y-m-d H:i", strtotime($lastUpdateDate)));
        if($domainStatus === 'REDEMPTION'  ){
            $redemption = true;
            $return = $this->formatWhoisDate(trim($lastUpdateDate), $redemption, $hold);
        } elseif ($domainStatus === 'DELETED' ){
            $redemption = false;
            $return = $this->formatWhoisDate(trim($lastUpdateDate), $redemption, $hold);
        } else {
            $redemption = false;
            $expireDate = $this->get_string_between($whois,"Expiry Date:", "created:");
            $return = $this->formatWhoisDate(trim($expireDate), $redemption, $hold);
        }
        $return['status']  = trim($domainStatus);
        $return['name'] = trim(htmlspecialchars($name));
        $return['lastUpdate'] = $lastUpdateDateFormatted->format('d/m/Y');
        $return['hold'] = $hold;
        return $return;
    }


    /**
     * Find interesting values in the result of whois
     * @param $string
     * @param $start
     * @param $end
     * @return string
     */
    protected function get_string_between($string, $start, $end){
        //var_dump(json_decode($string));
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    protected function getStatusFromWhois(string $whois, string $name)
    {
        $domainStatus = $this->get_string_between($whois,  $name  , "hold:" );
        $domainStatus = strstr($domainStatus, $name);
        $domainStatus = strstr($domainStatus, "status:");
        $domainStatus = strstr($domainStatus, "\n", true);
        $domainStatus = substr($domainStatus, 13 );

        return $domainStatus;
    }


    /**
     * Format Date return by Whois before save in Database
     * @param String $date
     * @return array
     */
    protected function formatWhoisDate(String $date, bool $redemption, bool $hold) : array
    {
        $formatedDate = new \DateTime(date("Y-m-d H:i", strtotime($date)));
        $formatedDate->setTimezone(new \DateTimeZone('Europe/Paris'));
        $minute = new \DateTime(date("d-m-Y H:i"));
        $minute->format('i');
        $domain = array();
        $expiryDate = $formatedDate;
        if($redemption) {
            $expiryDate = $formatedDate;
            $expiryDate->modify('+30 days');
            $domain['expiryDate'] =  $expiryDate->format('d/m/Y');
        } elseif ($redemption && $hold === false ){
            $expiryDate = $formatedDate;
            $expiryDate->modify('+60 days');
            $domain['expiryDate'] =  $expiryDate->format('d/m/Y');
        } else {
            $domain['expiryDate'] = $expiryDate->format('d/m/Y');
        }
        $expiryTime = $formatedDate;
        $domain['expiryTime'] = $expiryTime->format('H:i');
        $minute = date('i', strtotime($date));
        if($minute >= '32') {
            $expiryTime->modify('+1 hour');
            $domain['launchTime'] = $expiryTime->format('H') . ':22';
        } else {
            $domain['launchTime'] =  $expiryTime->format('H') . ':22';
        }

        return $domain;
    }

}