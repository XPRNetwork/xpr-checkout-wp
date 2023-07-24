<?php



class TokenPrices
{

  private $priceValidityInterval = 21600000;
  public function __construct()
  {
  }

  public function getTokenPrices()
  {

    // Do your code checking stuff here e.g. 
    $myPluginGateway = WC()->payment_gateways->payment_gateways()['woow'];

    $now = time();
    $savedPriceRatesValidity = $myPluginGateway->get_option('price_rates_validity');
    $savedPriceRates = $myPluginGateway->get_option('price_rates');
    //if (is_null($savedPriceRates) || $now > $savedPriceRatesValidity) {

    $headers = array(
      'Content-Type: application/json'
    );
    $url = "https://www.api.bloks.io/proton/tokens";
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $responseData = json_decode($response, true);
    //return $responseData;
    if ($responseData) {

      $rawFiltered = array_map(function ($token) {
        if (strpos($token['key'], 'test') !== false) return null;
        $tokenBase = [];
        $tokenBase['symbol'] = $token['symbol'];
        $tokenBase['contract'] = $token['account'];
        $tokenBase['decimals'] = $token['supply']['precision'];
        $prices =  array_filter($token['pairs'], function ($pair) {
          return strpos($pair['id'], '/USD') !== false;
        });
        if (isset($prices[0])) {

          return array_merge($prices[0], $tokenBase);
        } else {
          return null;
        }
      }, $responseData);

      return array_values(array_filter($rawFiltered, function ($token) {
        return !is_null($token);
      }));
    }
  }
}
