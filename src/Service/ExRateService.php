<?php


namespace App\Service;


use \Phpfastcache\Helper\Psr16Adapter;


/**
 * 
 */
class ExRateService {


  /**
   * @param array $codes коды валют
   * @param \DateTimeInterface $date дата торгов
   * @param \Phpfastcache\Helper\Psr16Adapter $cache кэш
   */
  public function __construct(private array $codes, private \DateTimeInterface $date, private Psr16Adapter $cache) {

  }


  /**
   * Получить данные по конвертации.
   * @return array
   */
  public function getRate(): array {

    $dateString = $this->date->format('d.m.Y');
    sort($this->codes);
    $cacheKey = sprintf('%s-%s', $dateString, implode('-', $this->codes));

    if ($rate = $this->cache->get($cacheKey)) {
      return $rate;
    }

    $xmlString = file_get_contents('https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $dateString);

    if ($xmlString === false) {
      
    }

    $xml = new \SimpleXMLElement($xmlString);

    $xpathSubSelectors = array_map(fn(string $code) => sprintf('CharCode = "%s"', $code), $this->codes);
    $xpathSubSelector = implode(' or ', $xpathSubSelectors);
    $xpathSelector = sprintf('/ValCurs/Valute[(%s)]', $xpathSubSelector);

    $data = $xml->xpath($xpathSelector);
    $rate = [$dateString => []];

    foreach ($data as $simpleXMLElement) {
      $rate[$dateString][$simpleXMLElement->CharCode->__toString()] = $simpleXMLElement->Value->__toString();
    }

    $this->cache->set($cacheKey, $rate, 3600);

    return $rate;
  }
}