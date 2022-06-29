<?php

namespace App\Controller\Domain;

use App\Controller\Trait\WhoisTrait;
use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]

Class SaveDomainController extends AbstractController
{
    use WhoisTrait;

    public function __construct(Domain $domain){

    }

    public function __invoke(Domain $data){

    }

}