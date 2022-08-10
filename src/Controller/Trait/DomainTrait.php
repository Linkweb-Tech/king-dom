<?php

namespace App\Controller\Trait;

use App\Controller\Domain\ChannelEPPController;

trait DomainTrait {
    protected function killAllConnexions(array $channels, string $domain)
    {
        foreach ($channels as $channel){
            $channel->killConnection($domain);
        }
    }
}