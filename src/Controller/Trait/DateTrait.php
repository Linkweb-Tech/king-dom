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
    protected function formatDate(String $date, bool $redemption) : array
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
//        if($redemption) {
//            $domain['expiryDate'] = date("d/m/Y", strtotime('+30 days', strtotime($date)));
//        } else {
//            $domain['expiryDate'] = date("d/m/Y", strtotime($date));
//        }
//
//        $domain['expiryTime']= date("H:i:s", strtotime($date));
//        $minute = date('i', strtotime($date));
//        if($minute > '32') {
//            $domain['launchTime'] = date("H", strtotime("+1 hour", strtotime($date))) . ':22';
//        } else {
//            $domain['launchTime'] = date("H", strtotime($date)). ':22';
//        }
        return $domain;
    }


    protected function getTodayFormatted() : \DateTime
    {
        $today = new \DateTime(date("d-m-Y H:i"));
        $today->setTimezone(new \DateTimeZone('Europe/Paris'));

        return $today;
    }

    protected function getTimeInMili() : \DateTime
    {

        $time = new \DateTime();
        $time->setTimezone(new \DateTimeZone('Europe/Paris'));
        $time->format("H:i:s.u");

        return $time;
    }
}