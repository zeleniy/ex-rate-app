<?php


use Workerman\Worker;
use App\Service\ExchangeService;


require_once __DIR__ . '/../../vendor/autoload.php';


// Create A Worker and Listens 2346 port, use Websocket protocol
$ws_worker = new Worker("websocket://0.0.0.0:2346");

// Emitted when data is received
$ws_worker->onMessage = function($connection, $data) {
  $connection->send(json_encode(ExchangeService::getInstance()->getRate()));
};

// Run worker
Worker::runAll();