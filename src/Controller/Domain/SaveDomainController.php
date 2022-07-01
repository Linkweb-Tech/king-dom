<?php

namespace App\Controller\Domain;

use App\Controller\Trait\WhoisTrait;
use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;



class SaveDomainController extends AbstractController
{
    use WhoisTrait;

    public function __construct(Domain $domain){
       // dd($domain);
    }

    #[Route('/whois', name: 'domain_whois')]
    public function index(Request $req): JsonResponse
    {
        //dd($req->query->get('domain'));
        $domain = $req->query->get('domain');
        $whois = $this->whois($domain);
        //dd($whois);
        return $this->json($whois);
    }

}