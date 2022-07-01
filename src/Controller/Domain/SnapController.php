<?php
namespace App\Controller\Domain;

use App\Controller\Domain\ChannelEPPController;
use App\Entity\Domain;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Trait\DateTrait;


class SnapController extends AbstractController
{

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    use DateTrait;
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(): array
    {

        $rhadamanthe= new ChannelEPPController($this->manager);
        $eaques = new ChannelEPPController($this->manager);
        $minos = new ChannelEPPController($this->manager);

        $minos->createConnexion('Minos');
        $rhadamanthe->createConnexion('Rhadamanthe');
        $eaques->createConnexion('Eaques');



        $domain = $minos->checkIfItsTime();
        //$domain = 'ladherence.fr';
        $today = $this->getTodayFormatted();
        $deadline = $this->getTodayFormatted()->modify("20 minutes");
        $altar = 'rhada';
        $exit = false;

        while($exit == false && $domain != false){
            if($today === $deadline){
                $exit = true;
            }
            if($altar === 'rhada'){
                usleep(540000);
                $rhadaResult = $rhadamanthe->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/result.txt', "\n $domain  Canal 1 : " . $rhadaResult, FILE_APPEND);
                //var_dump(\DateTime::createFromFormat('U.u', microtime(true)));
                $altar = 'eaques';
                if($rhadaResult == true){
                    echo 'Eaques snipe le domaine';
                    $eaques->snipeDomain($domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                $exit = $rhadaResult;
            } elseif($altar === 'eaques') {
                usleep(540000);
                $eaquesResult = $eaques->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/result.txt', "\n $domain  Canal 2 :" . $eaquesResult, FILE_APPEND);
                $altar = 'minos';
                if($eaquesResult === true){
                    echo 'Eaques snipe le domaine';
                    $minos->snipeDomain($domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                $exit = $eaquesResult;
            } else {
                usleep(540000);
                $minosResult = $minos->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/result.txt', "\n $domain  Canal 3 :" . $minosResult, FILE_APPEND );
                $altar = 'rhada';
                if($eaquesResult === true){
                    echo 'Eaques snipe le domaine';
                    $rhadamanthe->snipeDomain($domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                $exit = $minosResult;
            }
        }

        return ['return' => false, 'domain' => $domain ];
    }

    private function scanDomainAvailability()
    {

    }
}