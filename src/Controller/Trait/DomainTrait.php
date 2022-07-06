<?php

namespace App\Controller\Trait;

use App\Controller\Domain\ChannelEPPController;

trait DomainTrait {
    protected function killAllConnexions(ChannelEPPController $minos, ChannelEPPController $rhadamanthe, ChannelEPPController $eaques, string $domain)
    {
        $minos->killConnection($domain);
        $rhadamanthe->killConnection($domain);
        $eaques->killConnection($domain);
    }
}