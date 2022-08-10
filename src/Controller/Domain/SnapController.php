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
        $this->availablesChannels = $_ENV['AVAILABLES_CHANNELS'];
        $this->iteration = 1;
        $this->channels = [];

        // DEFAULT channel
        $this->channels[1] = new ChannelEPPController($this->manager);
        $this->channels[2] = new ChannelEPPController($this->manager);
        $this->channels[3] = new ChannelEPPController($this->manager);

        // RUSH channel
        if($this->availablesChannels > 3){
            for ($i = 4; $i <= $this->availablesChannels ; $i++) {
                $this->channels[$i] = new ChannelRushEPPController($this->manager);
            }
        }

        //Kill Connexions
        $this->killAllConnexions($this->channels, '');

        // Create Connexion for all channel
        foreach ($this->channels as $channel){
            $channel->createConnexion('');
        }
    }

    use DateTrait;
    use DomainTrait;

    /**
     * @throws \Exception
     */
    #[Route('/create-connexion', name:'create_connexion')]
    public function launchConnexion(): array
    {

        $today = $this->getTodayFormatted();
        $deadline = $this->getTodayFormatted()->modify("20 minutes");
        $first = $this->channels[1];
        $domain = $first->checkIfItsTime();


        if(!$domain->getName()) {
            dump('Aucun domain à snaper !');
            $this->killAllConnexions($this->channels, '');
            return ['return' => false, 'domain' => $domain->getName() ];
        }

        //$altar = 'rhada';
        $exit = 'no';
        $domainName = $domain->getName();

        while( $exit === 'no' ){
            $now  = $this->getTodayFormatted();
            if($now->format('H:i') === $deadline->format('H:i')){
                dump('Session terminée pour le ' . $domainName);
                $this->killAllConnexions($this->channels, $domainName);
                $exit = 'yes';
            }
            $this->scanDomainAvailability($this->channels[$this->iteration], $domainName);
            $this->iteration++;
            $this->iteration = ($this->iteration > $this->availablesChannels) ? 1 : $this->iteration;

        }

        return ['return' => false, 'domain' => $domain ];
    }

    private function scanDomainAvailability($currentChannel, $domainName)
    {
        $time =  $this->getTimeInMili();
        $TIME_BETWEEN_SNAP = 1600000 / $this->availablesChannels;

        usleep($TIME_BETWEEN_SNAP);
        $checkResult = $currentChannel->checkDomain($domainName);
        file_put_contents($this->cert_url.'logs/result-'. $domainName .'.txt', "\n $domainName  Canal " . $this->iteration . " : " . $checkResult .' :: ' . $time->format('H:i:s.u'), FILE_APPEND );
        if($checkResult === true){
            $next = $this->iteration + 1;
            $nextChannel = $this->channels[$next];
            echo 'Canal_'. $next .' snipe le domaine';
            $nextChannel->snipeDomain($domainName);
            $this->killAllConnexions($this->channels,  $domainName);
            return ['return' => true, 'domain' => $domainName ];
        }
        return ['return' => false ];
    }
}