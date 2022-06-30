<?php

namespace App\Controller\Trait;
/**
 * Trait Date for all Date manipulation.
 */
trait DateTrait
{

    /**
     * Format Date return by Whois before save in Database
     * @param String $date
     * @return array
     */
    protected function formatDate(String $date) : array
    {
        $minute = new \DateTime(date("d-m-Y H:i"));
        $minute->format('i');
        $domain = array();
        $domain['expiryDate'] = date("d/m/Y", strtotime($date));
        $domain['expiryTime']= date("H:i:s", strtotime($date));
        $minute = date('i', strtotime($date));
        if($minute > '32') {
            $domain['launchTime'] = date("H", strtotime("+1 hour", strtotime($date))) . ':22';
        } else {
            $domain['launchTime'] = date("H", strtotime($date)). ':22';
        }
        return $domain;
    }


    protected function getTodayFormatted() : \DateTime
    {
        $today = new \DateTime(date("d-m-Y H:i"));
        $today->setTimezone(new \DateTimeZone('Europe/Paris'));

        return $today;
    }
}