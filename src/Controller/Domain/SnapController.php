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
    }

    use DateTrait;
    use DomainTrait;
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(): array
    {

        $rhadamanthe= new ChannelEPPController($this->manager);
        $eaques = new ChannelEPPController($this->manager);
        $minos = new ChannelEPPController($this->manager);


        $minos->createConnexion('Minos');
        $rhadamanthe->createConnexion('Rhadamanthe');
        $eaques->createConnexion('Eaques');
        $today = $this->getTodayFormatted();
        $deadline = $this->getTodayFormatted()->modify("20 minutes");
        $domain = $minos->checkIfItsTime();
    dump($domain);
        //$domain = 'linkweb.fr';
        $altar = 'rhada';
        $exit = 'no';
        dump($exit);
        //file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/logs/result-'. $domain .'.txt', "\nDébut du process pour :  " . $domain , FILE_APPEND);
        while( $exit === 'no' ){
            dump('on y est ');
            $now  = $this->getTodayFormatted();
            if($now->format('H:i') === $deadline->format('H:i')){
                dump('Session terminée pour le ' . $domain);
                $minos->killConnection($domain);
                $rhadamanthe->killConnection($domain);
                $eaques->killConnection($domain);
                $exit = 'yes';
            }
            if($altar === 'rhada'){
               $time =  $this->getTimeInMili();
                usleep(460000);
                $rhadaResult = $rhadamanthe->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/logs/result-'. $domain .'.txt', "\n $domain  Canal 1 : " . $rhadaResult .' :: ' .  $time->format('H:i:s.u'), FILE_APPEND);
                //var_dump(\DateTime::createFromFormat('U.u', microtime(true)));
                $altar = 'eaques';
                if($rhadaResult == true){
                    echo 'Eaques snipe le domaine';
                    $eaques->snipeDomain($domain);
                    $this->killAllConnexions($minos, $rhadamanthe, $eaques, $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                //$exit = $rhadaResult;
            } elseif($altar === 'eaques') {
                $time =  $this->getTimeInMili();
                usleep(460000);
                $eaquesResult = $eaques->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/logs/result-'. $domain .'.txt', "\n $domain  Canal 2 : " . $eaquesResult.' :: ' . $time->format('H:i:s.u'), FILE_APPEND);
                $altar = 'minos';
                if($eaquesResult === true){
                    echo 'Eaques snipe le domaine';
                    $minos->snipeDomain($domain);
                    $this->killAllConnexions($minos, $rhadamanthe, $eaques, $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                //$exit = $eaquesResult;
            } else {
                $time =  $this->getTimeInMili();
                usleep(460000);
                $minosResult = $minos->checkDomain($domain);
                file_put_contents('/Users/nicolas_candelon/Documents/Projects/king-dom/logs/result-'. $domain .'.txt', "\n $domain  Canal 3 : " . $minosResult .' :: ' . $time->format('H:i:s.u'), FILE_APPEND );
                $altar = 'rhada';
                if($eaquesResult === true){
                    echo 'Eaques snipe le domaine';
                    $rhadamanthe->snipeDomain($domain);
                    $this->killAllConnexions($minos, $rhadamanthe, $eaques, $domain);
                    return ['return' => true, 'domain' => $domain ];
                }
                //$exit = $minosResult;
            }

        }

        return ['return' => false, 'domain' => $domain ];
    }

    private function scanDomainAvailability()
    {

    }
}