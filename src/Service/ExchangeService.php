<?php


namespace App\Service;


use \Phpfastcache\Helper\Psr16Adapter;


/**
 *
 */
class ExchangeService {


  /**
   * Кэш.
   * @var Psr16Adapter
   */
  private $cache;


  /**
   * 
   */
  private function __construct() {

    $this->cache = new Psr16Adapter('Files');
  }


  /**
   * Получить инстанс класса.
   * @return ExchangeService
   */
  public static function getInstance(): ExchangeService {

    return new ExchangeService();
  }


  /**
   * Получить данные по курсам валют.
   * @return array
   */
  public function getRate(): array {

    $today = new \DateTimeImmutable();
    $yesterday = $today->sub(new \DateInterval("P1D"));

    $rateToday  = new ExRateService(['USD', 'EUR'], $today, $this->cache);
    $rateBefore = new ExRateService(['USD', 'EUR'], $yesterday, $this->cache);

    return [
      'time' => date('H:i:s d.m.Y'),
      'rate' => [ $rateToday->getRate(), $rateBefore->getRate() ]
    ];
  }
}