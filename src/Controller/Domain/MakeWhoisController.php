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
        name: 'whois_domain',
        path: '/domain/{id}/whois',
        methods: ['GET'],
        defaults: [
        '_api_resource_class' => Domain::class,
        '_api_item_operation_name' => 'whois_domain',
    ],
    )]
    public function __invoke(Domain $data) : array
    {
//        $name = $data->getName();
//        $whois = shell_exec("whois $name");
//        $domainStatus = $this->get_string_between($whois, $name ."\nstatus:", "\nhold:" );
//        $expireDate = $this->get_string_between($whois,"Expiry Date:", "created:");
//        $expireDate = trim($expireDate);
//        $return = $this->formatDate($expireDate);
//        $return[]  = trim($domainStatus);
//        return $return;

        $name = $data->getName();
        return $this->whois($name);
    }


}
