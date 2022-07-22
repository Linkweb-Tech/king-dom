<?php


namespace App\Controller\Domain;
use App\Controller\Domain\ChannelEPPController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


class ChannelRushEPPController extends ChannelEPPController
{
    public function __construct(EntityManagerInterface $entityManager)
    {

        parent::__construct($entityManager);
        $this->host = $_ENV['HOST_RUSH'];

    }


    /**
     * Create connexion for one channel at a time
     * @param string $name
     * @return JsonResponse
     */
    public function createConnexion(string $name)
    {
        $this->name = $name;
        $context = stream_context_create(array('ssl' => array('local_cert' => $this->cert_url.'src/Controller/Domain/LINKWEB_SARL_afnic_cert+key.pem',"verify_peer" => false,"verify_peer_name"=>true)));

        $this->fp = stream_socket_client('ssl://'.$this->host.':'.$this->port, $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $context);
        $xlogin = htmlspecialchars($this->login, ENT_XML1);
        $xpw = htmlspecialchars($this->password, ENT_XML1);
        if (! $this->fp) {
            exit("ERROR: $errno - $errstr<br />\n");
        }
        $frame = $this->receive($this->fp);
        $buffer = "<?xml version='1.0' encoding='UTF-8'  standalone='no'?><epp xmlns='urn:ietf:params:xml:ns:epp-1.0' >
            <command>
                <login><clID>$xlogin</clID><pw>$xpw</pw><options><version>1.0</version><lang>fr</lang></options><svcs><objURI>urn:ietf:params:xml:ns:contact-1.0</objURI><objURI>urn:ietf:params:xml:ns:domain-1.0</objURI><objURI>urn:ietf:params:xml:ns:host-1.0</objURI><svcExtension><extURI>urn:ietf:params:xml:ns:rgp-1.0</extURI><extURI>http://www.afnic.fr/xml/epp/frnic-1.4</extURI></svcExtension></svcs></login>
            </command>
            </epp>";
        fwrite($this->fp, pack('N', 4 + strlen($buffer)));
        fwrite($this->fp, $buffer);
        $frame = $this->receive($this->fp);
        $connexion['name'] = $this->name;
        $connexion['fp'] = $this->fp;
        return true;
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