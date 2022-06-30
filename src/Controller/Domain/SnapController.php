<?php
namespace App\Controller\Domain;

use App\Controller\Domain\ChannelEPPController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SnapController extends AbstractController
{
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(Domain $domain): JsonResponse
    {
        $rhadamanthe= new ChannelEPPController;
        $eaques = new ChannelEPPController;
        $minos = new ChannelEPPController;

        $minos->createConnexion('Minos');
        $rhadamanthe->createConnexion('Rhadamanthe');
        $eaques->createConnexion('Eaques');



        $check = $minos->checkIfItsTime('baszszc44.fr');

        return $this->json($check);
    }

    private function scanDomainAvailability()
    {

    }
}