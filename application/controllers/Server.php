<?php
require './vendor/autoload.php';
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Server extends CI_Controller
{
	 public function __construct() {
		parent::__construct();
		$this->load->library('session');
		$this->load->library('chat');
	}
	public function index()
	{
		$loop   = React\EventLoop\Factory::create();
		$webSock = new \React\Socket\SecureServer(
			new Server('0.0.0.0:8090', $loop),
			$loop,
			array(
				'local_cert'        => './server/my_cert.crt', // path to your cert
				'local_pk'          => './server/my_key.key', // path to your server private key
				'allow_self_signed' => TRUE, // Allow self signed certs (should be false in production)
				'verify_peer' => FALSE
			)
		);
		
		$server = IoServer::factory(
			new HttpServer(
				new WsServer(
					new Chat()
				)
			),
			$webSock
		);
	}

	//--------------------------------------------------------------------

}