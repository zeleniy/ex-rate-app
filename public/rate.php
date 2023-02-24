<?php


require_once '../vendor/autoload.php';


use App\Service\ExRateService;
use App\Cache\FileCache;
use Phpfastcache\Helper\Psr16Adapter;


$defaultDriver = 'Files';
$cache = new Psr16Adapter($defaultDriver);

$today = new \DateTimeImmutable();
$yesterday = $today->sub(new \DateInterval("P1D"));

$rateToday  = new ExRateService(['USD', 'EUR'], $today, $cache);
$rateBefore = new ExRateService(['USD', 'EUR'], $yesterday, $cache);

header('Content-Type: application/json');
echo json_encode([
  'time' => date('H:i:s d.m.Y', $_SERVER['REQUEST_TIME']),
  'rate' => $rateToday->getRate() + $rateBefore->getRate()
]);