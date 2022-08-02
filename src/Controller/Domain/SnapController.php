<?php
namespace App\Controller\Domain;

use App\Controller\Domain\ChannelEPPController;
use App\Controller\Trait\DomainTrait;
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
        $this->cert_url = $_ENV['CERT_URL'];
    }

    use DateTrait;
    use DomainTrait;

    /**
     * @throws \Exception
     */
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(): array
    {

        // Instance of Channel
        $hypnos = new ChannelRushEPPController($this->manager);
        $rhadamanthe= new ChannelEPPController($this->manager);
        $eaques = new ChannelEPPController($this->manager);
        $minos = new ChannelEPPController($this->manager);

        // Connexions of all Channel
        $rhadamanthe->createConnexion('Rhadamanthe');
        $eaques->createConnexion('Eaques');
        $minos->createConnexion('Minos');
        $hypnos->createConnexion('Hypnos');


        $today = $this->getTodayFormatted();
        $deadline = $this->getTodayFormatted()->modify("20 minutes");
        $domain = $hypnos->checkIfItsTime();

        if(!$domain) {
            dump('Aucun domain à snaper !');
            return ['return' => false, 'domain' => $domain ];
        }

       $altar = 'rhada';
        $exit = 'no';
        while( $exit === 'no' ){
            $now  = $this->getTodayFormatted();
            if($now->format('H:i') === $deadline->format('H:i')){
                dump('Session terminée pour le ' . $domain);
                $this->killAllConnexions($minos, $rhadamanthe, $eaques, $hypnos, $domain);
                $exit = 'yes';
            }
            if($altar === 'rhada'){
               $time =  $this->getTimeInMili();
                usleep(360000);
                $rhadaResult = $rhadamanthe->checkDomain($domain);
                file_put_contents($this->cert_url.'logs/result-'. $domain .'.txt', "\n $domain  Canal 1 : " . $rhadaResult .' :: ' .  $time->format('H:i:s.u'), FILE_APPEND);
                $altar = 'eaques';
                if($rhadaResult == true){
                    echo 'Eaques snipe le domaine';
                    $eaques->snipeDomain($domain);
                   $this->killAllConnexions($minos, $rhadamanthe, $eaques, $hypnos, $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
            } elseif($altar === 'eaques') {
                $time =  $this->getTimeInMili();
                usleep(360000);
                $eaquesResult = $eaques->checkDomain($domain);
                file_put_contents($this->cert_url.'logs/result-'. $domain .'.txt', "\n $domain  Canal 2 : " . $eaquesResult.' :: ' . $time->format('H:i:s.u'), FILE_APPEND);
                $altar = 'hypnos';
                if($eaquesResult === true){
                    echo 'Eaques snipe le domaine';
                    $hypnos->snipeDomain($domain);
                   $this->killAllConnexions($minos, $rhadamanthe, $eaques, $hypnos,  $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
            } elseif ($altar === 'hypnos'){
                $time =  $this->getTimeInMili();
                usleep(360000);
                $hypnosResult = $hypnos->checkDomain($domain);
                file_put_contents($this->cert_url.'logs/result-'. $domain .'.txt', "\n $domain  Canal 3 : " . $hypnosResult .' :: ' . $time->format('H:i:s.u'), FILE_APPEND );
                $altar = 'minos';
                if($hypnosResult === true){
                    echo 'Rhadamanthe snipe le domaine';
                    $minos->snipeDomain($domain);
                  $this->killAllConnexions($minos, $rhadamanthe, $eaques, $hypnos,  $domain);
                    return ['return' => true, 'domain' => $domain ];
               }
            } elseif($altar = 'minos'){
                $time =  $this->getTimeInMili();
                usleep(360000);
                $minosResult = $minos->checkDomain($domain);
                file_put_contents($this->cert_url.'logs/result-'. $domain .'.txt', "\n $domain  Canal 4 : " . $minosResult .' :: ' . $time->format('H:i:s.u'), FILE_APPEND );
                $altar = 'rhada';
                if($minosResult === true){
                    echo 'Rhadamanthe snipe le domaine';
                    $rhadamanthe->snipeDomain($domain);
                    $this->killAllConnexions($minos, $rhadamanthe, $eaques, $hypnos,  $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
            } else {

            }
        }

        return ['return' => false, 'domain' => $domain ];
    }

    private function scanDomainAvailability()
    {

    }
}