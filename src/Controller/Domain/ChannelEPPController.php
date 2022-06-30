<?php
namespace App\Controller\Domain;

use App\Entity\Domain;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;



class ChannelEPPController extends AbstractController
{
    public function __construct()
    {
//        $dotenv = new Dotenv();
//        $dotenv->load(__DIR__.'/.env');
        $this->host = $_ENV['HOST'];
        $this->port = $_ENV['PORT'];
        $this->cert = $_ENV['CERT'];
        $this->login = $_ENV['LOGIN'];
        $this->password = $_ENV['PASSWORD'];

    }

    /**
     * @param DomainRepository $domainRepository
     * @return JsonResponse
     * @throws \Exception
     */
    #[Route('/check-domain-expiration', name: 'check-domain-expiration')]
    public function checkIfItsTime(DomainRepository $domainRepository): JsonResponse
    {
        $domains = $domainRepository->findAll();
        //$minute = ( new \DateTime)->format('i');
        $today = new \DateTime(date("d-m-Y H:i"));
        $today->setTimezone(new \DateTimeZone('Europe/Paris'));
        foreach ($domains as $domain){
            $completeTime = $domain->getExpiryDate().' '. $domain->getLaunchTime();
            $completeTimeFormatted = str_replace('/', '-', $completeTime);
            $connexionTime =  new \DateTime(date("d-m-Y H:i", strtotime($completeTimeFormatted)));
            if($today->format('d/m/Y H:i') == $connexionTime->format('d/m/Y H:i')){
                return $this->json(['time' => $today->modify("20 minutes")]);
            } else {
                echo 'Its not time';
                return $this->json(['time' => 'false']);
            }
        }
    }



    public function createConnexions(string $name): JsonResponse
    {
        $this->name = $name;
        $context = stream_context_create(array('ssl' => array('local_cert' => $this->cert,"verify_peer" => true,"verify_peer_name"=>false)));

        $this->fp = stream_socket_client('ssl://'.$this->host.':'.$this->port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        $xlogin = htmlspecialchars($this->login, ENT_XML1);
        $xpw = htmlspecialchars($this->password, ENT_XML1);

        if (! $this->fp) {
            exit("ERROR: $errno - $errstr<br />\n");
        }
        $frame = $this->receive($this->fp);
        $buffer = "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
            <command>
                <login><clID>$xlogin</clID><pw>$xpw</pw><options><version>1.0</version><lang>en</lang></options><svcs><objURI>urn:ietf:params:xml:ns:contact-1.0</objURI><objURI>urn:ietf:params:xml:ns:domain-1.0</objURI><objURI>urn:ietf:params:xml:ns:host-1.0</objURI><svcExtension><extURI>urn:ietf:params:xml:ns:rgp-1.0</extURI><extURI>http://www.afnic.fr/xml/epp/frnic-1.4</extURI></svcExtension></svcs></login>
            </command>
            </epp>";
        fwrite($this->fp, pack('N', 4 + strlen($buffer)));
        fwrite($this->fp, $buffer);
        $frame = $this->receive($this->fp);
        $connexion['name'] = $this->name;
        $connexion['fp'] = $this->fp;
        //$connexion = $this->keepConnectionAlive($this->fp, $this->name);
        return $this->json($connexion);
    }


    #[Route('/create-connexion', name:'create_connexion')]
    public function declareConnexion(): JsonResponse
    {
        $minos = new ChannelEPPController();
        //dd($this->getParameter('kernel.project_dir'));
        dd($minos->createConnexions('Minos'));

    }
}

