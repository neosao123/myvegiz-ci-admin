<?php

require __DIR__ . "/../vendor/autoload.php";

use Chat\Chat;

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

 $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        8085,
        '162.240.44.23'
    );

    $server->run();
?>
