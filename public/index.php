<?php


use Workerman\Worker;
use App\Service\ExRateService;
use App\Cache\FileCache;
use Phpfastcache\Helper\Psr16Adapter;
use App\Service\ExchangeService;


require_once __DIR__ . '/../vendor/autoload.php';


// Create A Worker and Listens 2346 port, use Websocket protocol
$ws_worker = new Worker("websocket://0.0.0.0:2346");

// Emitted when new connection come
$ws_worker->onConnect = function($connection)
{
  // Emitted when websocket handshake done
  $connection->onWebSocketConnect = function($connection)
  {
    echo "New connection\n";
  };
};

// Emitted when data is received
$ws_worker->onMessage = function($connection, $data) {

  $connection->send(json_encode(ExchangeService::getInstance()->getRate()));
};

// Emitted when connection closed
$ws_worker->onClose = function($connection)
{
  echo "Connection closed";
};

// Run worker
Worker::runAll();