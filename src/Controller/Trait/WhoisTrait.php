<?php

namespace App\Controller\Trait;



/**
 * Trait WhoisTrait.
 */
trait WhoisTrait
{
    protected function whois(String $name)
    {
        $whois = shell_exec("whois $name");
        $domainStatus = $this->get_string_between($whois, $name ."\nstatus:", "\nhold:" );
        $expireDate = $this->get_string_between($whois,"Expiry Date:", "created:");
        $expireDate = trim($expireDate);
        $return = $this->formatDate($expireDate);
        $return[]  = trim($domainStatus);
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

    /**
     * Format Date return by Whois before save in Database
     * @param String $date
     * @return array
     */
    protected function formatDate(String $date) : array
    {
        $domain = array();
        $domain['expiryDate'] = date("d/m/Y", strtotime($date));
        $domain['expiryTime']= date("H:i:s", strtotime($date));
        $minute = date('i', strtotime($date));
        if($minute > '32') {
            $domain['launchTime'] = date("H:i", strtotime("+1 hour", strtotime($date)));
        } else {
            $domain['launchTime'] = date("H:i", strtotime($date));
        }

        return $domain;
    }
}