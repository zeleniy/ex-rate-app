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

    sort($this->codes);
  }


  /**
   * Получить данные по конвертации.
   * @return array
   */
  public function getRate(): array {

    $dateString = $this->date->format('d.m.Y');

    try {

      $cacheKey = sprintf('%s-%s', $dateString, implode('-', $this->codes));

      if ($rate = $this->cache->get($cacheKey)) {
        return $rate;
      }

      $xmlString = file_get_contents('https://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $dateString);

      if ($xmlString === false) {
        return $this->getEmptyRate($dateString);
      }

      $rate = [
        'data' => $dateString,
        'rates' => []
      ];

      foreach ($this->parseXml($xmlString) as $simpleXMLElement) {
        $rate['rates'][$simpleXMLElement->CharCode->__toString()] = str_replace(',', '.', $simpleXMLElement->Value->__toString());
      }

      $this->cache->set($cacheKey, $rate, 3600);

      return $rate;

    } catch (\Throwable $e) {

      return $this->getEmptyRate($dateString);
    }
  }


  /**
   * Извлечь данные их XML.
   * @param string $xmlString XML
   * @return array
   */
  private function parseXml(string $xmlString): array {

    $xml = new \SimpleXMLElement($xmlString);

    $xpathSubSelectors = array_map(fn(string $code) => sprintf('CharCode = "%s"', $code), $this->codes);
    $xpathSubSelector = implode(' or ', $xpathSubSelectors);
    $xpathSelector = sprintf('/ValCurs/Valute[(%s)]', $xpathSubSelector);

    return $xml->xpath($xpathSelector);
  }


  /**
   * Получить пустой набор данных.
   * @param  string $dateString дата в формате d.m.Y
   * @return array
   */
  private function getEmptyRate(string $dateString): array {

    return [
      'data' => $dateString,
      'rates' => array_combine($this->codes, array_fill(0, count($this->codes), null))
    ];
  }
  
}