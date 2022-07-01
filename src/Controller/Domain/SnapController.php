<?php
namespace App\Controller\Domain;

use App\Controller\Domain\ChannelEPPController;
use App\Entity\Domain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SnapController extends AbstractController
{
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(): JsonResponse
    {
        $rhadamanthe= new ChannelEPPController;
        $eaques = new ChannelEPPController;
        $minos = new ChannelEPPController;

        $minos->createConnexion('Minos');
        $rhadamanthe->createConnexion('Rhadamanthe');
        $eaques->createConnexion('Eaques');



        $check = $minos->checkIfItsTime();


        return $this->json($check);
    }

    private function scanDomainAvailability()
    {

    }
}