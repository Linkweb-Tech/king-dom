<?php

namespace App\Controller\Domain;

use App\Controller\Trait\WhoisTrait;
use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class MakeWhoisController extends AbstractController
{
    use WhoisTrait;
    public function __construct(Domain $domain){

    }


    #[Route(
        path: '/domain/{id}/whois',
        name: 'whois_domain',
        defaults: [
        '_api_resource_class' => Domain::class,
        '_api_item_operation_name' => 'whois_domain',
        ],
        methods: ['GET'],
    )]
    public function __invoke(Domain $data) : array
    {
        $name = $data->getName();
        return $this->whois($name);
    }


}
