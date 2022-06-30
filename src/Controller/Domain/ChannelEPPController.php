<?php
namespace App\Controller\Domain;

use App\Controller\Trait\DateTrait;
use App\Entity\Domain;
use App\Controller\Trait\WhoisTrait;
use App\Repository\DomainRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;





class ChannelEPPController extends AbstractController
{
    use WhoisTrait;
    use DateTrait;
    public function __construct()
    {
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
        $today = $this->getTodayFormatted();
        foreach ($domains as $domain){
            $completeTime = $domain->getExpiryDate().' '. $domain->getLaunchTime();
            $completeTimeFormatted = str_replace('/', '-', $completeTime);
            $connexionTime =  new \DateTime(date("d-m-Y H:i", strtotime($completeTimeFormatted)));
            if($today->format('d/m/Y H:i') == $connexionTime->format('d/m/Y H:i')){
                return $this->json(['time' => $today->modify("20 minutes")]);
            } else {
                return $this->json(['time' => 'false']);
            }
        }
    }


    /**
     * Create connexion for one channel at a time
     * @param string $name
     * @return JsonResponse
     */
    public function createConnexion(string $name)
    {
        $this->name = $name;
        $context = stream_context_create(array('ssl' => array('local_cert' => '/Users/nicolas_candelon/Documents/Projects/king-dom/src/Controller/Domain/LINKWEB_SARL_afnic_cert+key.pem',"verify_peer" => false,"verify_peer_name"=>false)));
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
        return true;
    }

    /**
     * Check the availability of a domain if True launch snipe
     * @param $domain
     * @return mixed
     */
    public function checkdomain($domain){
        $buffer3 =  "<?xml version='1.0' encoding='UTF-8'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
           <command>
             <check>
            <domain:check xmlns:domain='urn:ietf:params:xml:ns:domain-1.0'>
            <domain:name>$domain</domain:name>
            </domain:check>
             </check>
             <clTRID>dsfdkvbdsgcbdgsc</clTRID>
             </command>
            </epp>";
        fwrite($this->fp, pack('N', 4 + strlen($buffer3)));
        fwrite($this->fp, $buffer3);
        $frame = $this->receive($this->fp);
        //$xml = simplexml_load_string($frame);
        $parsed = $this->get_string_between($frame, 'avail="', '">');
        return $parsed;
    }

    /**
     * Simple function to simulate a test of check domain. It return True after somes minutes
     * @return bool
     */
    public function simulateCheckDomain(){
        $today =  \DateTime::createFromFormat('i', date("i"));
        $today->setTimezone(new \DateTimeZone('Europe/Paris'));
        if($today->format('i') == 14){
            return true;
        } else {
            return false;
        }
    }

    /**
     * Launch the special attack or just try to snipe the domain
     * @param string $domain
     * @return JsonResponse
     */
    public function snipeDomain(string $domain)
    {
        $buffer = '<?xml version="1.0"?>
            <epp xmlns="urn:ietf:params:xml:ns:epp-1.0">
             <command>
             <create>
            <domain:create xmlns:domain="urn:ietf:params:xml:ns:domain-1.0">
            <domain:name>linkweb.fr</domain:name>
            <domain:period unit="y">1</domain:period>
            <domain:registrant>MP61713</domain:registrant>
            <domain:contact type="admin">MP61713</domain:contact>
            <domain:contact type="tech">NC32276</domain:contact>
            <domain:authInfo>
            <domain:pw>iv2252UtF8N/kF7atGH3iCaf</domain:pw>
            </domain:authInfo>
            </domain:create>
             </create>
             <clTRID>TMcF3I+zGO1VS5gO7pJWDkVn</clTRID>
             </command>
            </epp>';
        fwrite($this->fp, pack('N', 4 + strlen($buffer)));
        fwrite($this->fp, $buffer);
        $frame = $this->receive($this->fp);
        file_put_contents('cronTest.txt', json_encode($frame));
        return $this->json($frame);
    }

    private function fullread($fp, $count) {
        $readBuffer = "";
        while ($count > 0) {
            $data = fread($fp, $count);
            if ($data === FALSE) {
                die("ERROR: fread failed");
            }
            $count -= strlen($data);
            $readBuffer .= $data;
        }
        return $readBuffer;
    }

    private function receive($fp) {
        $data = $this->fullread($fp, 4);
        $count = unpack('N', $data);
        $count = $count[1];
        $buffer = $this->fullread($fp, $count - 4);
        return $buffer;
    }
}

