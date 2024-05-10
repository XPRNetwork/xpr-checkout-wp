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
    $myPluginGateway = WC()->payment_gateways->payment_gateways()['wookey'];

    $now = time();
    $savedPriceRatesValidity = $myPluginGateway->get_option('price_rates_validity');
    $savedPriceRates = $myPluginGateway->get_option('price_rates');
    if (is_null($savedPriceRates) || $now > $savedPriceRatesValidity) {

      $url = "https://api.freecurrencyapi.com/v1/latest";
      $ch = curl_init($url);
      $headers = array(
        "apikey: " . $this->apiKey,
        'Content-Type: application/json'
      );

      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
      curl_setopt($ch, CURLOPT_POSTFIELDS, '{"base_currency": "USD"}');
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $response = curl_exec($ch);
      curl_close($ch);
      $responseData = json_decode($response, true);

      $myPluginGateway->update_option('price_rates_validity', $now + $this->priceValidityInterval);
      $myPluginGateway->update_option('price_rates', serialize($responseData['data']));
      $savedPriceRates = $myPluginGateway->get_option('price_rates');
    }

    $rates = unserialize($savedPriceRates);
    $prices = [];
    $sql = "INSERT INTO wp_".WOOKEY_TABLE_FIAT_RATES." (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f";
      $sql = $wpdb->prepare($sql,'USD',1,'USD');
      $res = $wpdb->query($sql);
      
    foreach ($rates as $symbol => $rate) {
      
      $sql = "INSERT INTO wp_".WOOKEY_TABLE_FIAT_RATES." (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f";
      $sql = $wpdb->prepare($sql,$symbol,$rate,$rate);
      $res = $wpdb->query($sql);
      if ($currency == $symbol) return $usdAmount / $rate;
    }
    return $usdAmount;
  }
}
