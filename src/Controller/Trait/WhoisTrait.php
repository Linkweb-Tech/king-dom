<?php

namespace App\Controller\Trait;
use App\Controller\Trait\DateTrait;
/**
 * Trait WhoisTrait.
 */
trait WhoisTrait
{
    use DateTrait;
    protected function whois(String $name)
    {
        $whois = shell_exec("whois $name");
        $domainStatus = $this->getStatusFromWhois($whois, $name);
        if($domainStatus === 'REDEMPTION'){
            $lastUpdateDate = $this->get_string_between($whois,"last-update:", "\n");
            $return = $this->formatDate(trim($lastUpdateDate), true);
        } else {
            $expireDate = $this->get_string_between($whois,"Expiry Date:", "created:");
            $return = $this->formatDate(trim($expireDate), false);
        }

        $return['status']  = trim($domainStatus);
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

}