<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
class XPRCheckout_PriceRateRPC
{

  private $priceValidityInterval = 21600000;
  public function __construct($apiKey)
  {
    $this->apiKey = $apiKey;
  }

  public function getUSDConvertionRate($currency = "EUR", $amount = 10)
  {

    global $wpdb;
    $xprCheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];

    $now = time();
    $savedPriceRatesValidity = $xprCheckoutGateway->get_option('price_rates_validity');
    $savedPriceRates = $xprCheckoutGateway->get_option('price_rates');
    $unserializePriceRates = unserialize($savedPriceRates);
    
    if (is_null($unserializePriceRates) || $now > $savedPriceRatesValidity) {

      $url = XPRCHECKOUT_PRICE_RATE_API_ENDPOINT;
      $headers = array(
        "apikey"=> $this->apiKey,
        "Content-Type"=>"application/json"
      );
      $response = wp_remote_get($url, array(
        'headers' => $headers,
        'body' => array('base_currency' => 'USD')
      ));

      if (is_wp_error($response)) {
        return $amount; // Handle error
      }
      
      $responseData = json_decode(wp_remote_retrieve_body($response), true);

      $xprCheckoutGateway->update_option('price_rates_validity', $now + $this->priceValidityInterval);
      $xprCheckoutGateway->update_option('price_rates', serialize($responseData['data']));
      $savedPriceRates = $xprCheckoutGateway->get_option('price_rates');
    }

    $rates = unserialize($savedPriceRates);
    $prices = [];
    
      //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,
      $res = $wpdb->query(
        //phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
        $wpdb->prepare("INSERT INTO wp_%1s (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f",XPRCHECKOUT_TABLE_FIAT_RATES,'USD',1,'USD')
      );
      
    foreach ($rates as $symbol => $rate) {
      
      //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
      $res = $wpdb->query(
        //phpcs:ignore WordPress.DB.PreparedSQLPlaceholders.UnquotedComplexPlaceholder
        $wpdb->prepare("INSERT INTO wp_%1s (symbol,rate) VALUES (%s,%.8f) ON DUPLICATE KEY UPDATE rate = %.8f",XPRCHECKOUT_TABLE_FIAT_RATES,$symbol,$rate,$rate)
      );
      if ($currency == $symbol) return $amount / $rate;
    }
    return $amount;
  }
}
