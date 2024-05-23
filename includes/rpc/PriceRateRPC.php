<?php

class PriceRateRPC
{

  private $priceValidityInterval = 21600000;
  public function __construct($apiKey)
  {
    $this->apiKey = $apiKey;
  }

  public function getUSDConvertionRate($currency = "EUR", $usdAmount = 10)
  {

    global $wpdb;
    $myPluginGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];

    $now = time();
    $savedPriceRatesValidity = $myPluginGateway->get_option('price_rates_validity');
    $savedPriceRates = $myPluginGateway->get_option('price_rates');
    if (is_null($savedPriceRates) || $now > $savedPriceRatesValidity) {

      $url = "https://api.freecurrencyapi.com/v1/latest";
      $headers = array(
        "apikey: " . $this->apiKey,
        'Content-Type: application/json'
      );

      $response = wp_remote_get($url, array(
        'headers' => $headers,
        'body' => json_encode(array('base_currency' => 'USD'))
      ));

      if (is_wp_error($response)) {
        return $usdAmount; // Handle error
      }

      $responseData = json_decode(wp_remote_retrieve_body($response), true);

      $myPluginGateway->update_option('price_rates_validity', $now + $this->priceValidityInterval);
      $myPluginGateway->update_option('price_rates', serialize($responseData['data']));
      $savedPriceRates = $myPluginGateway->get_option('price_rates');
    }

    $rates = unserialize($savedPriceRates);
    $prices = [];
    $sql = "INSERT INTO wp_".XPRCHECKOUT_TABLE_FIAT_RATES." (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f";
      $sql = $wpdb->prepare($sql,'USD',1,'USD');
      $res = $wpdb->query($sql);
      
    foreach ($rates as $symbol => $rate) {
      
      $sql = "INSERT INTO wp_".XPRCHECKOUT_TABLE_FIAT_RATES." (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f";
      $sql = $wpdb->prepare($sql,$symbol,$rate,$rate);
      $res = $wpdb->query($sql);
      if ($currency == $symbol) return $usdAmount / $rate;
    }
    return $usdAmount;
  }
}
