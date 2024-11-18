<?php

class TokenPrices
{
  private $priceValidityInterval = 21600000;

  public function __construct()
  {
  }

  public function getTokenPrices()
  {
    global $wpdb;
    $paymentGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];

    
    $url = "https://www.api.bloks.io/proton/tokens";

    $response = wp_remote_get($url, array(
      'headers' => array(
        'Accept' => 'application/json',
        'Content-Type' => 'application/json'
      ),
      'timeout' => 45,
      'sslverify' => false // Only if needed for testing
    ));
    
    if (is_wp_error($response)) {
      
      return null;
    }
    
    $json_data = wp_remote_retrieve_body($response);
        
    try {
      // Remove any BOM characters and clean the JSON string
      $json_data = preg_replace_callback(
        '/\\\\u(d[89ab][0-9a-f]{2})(?!\\\\u(d[c-f][0-9a-f]{2}))/i',
        function ($matches) {
            return '';
        },
        $json_data
    );
    
    // Remove unpaired low surrogates
    $json_data = preg_replace_callback(
        '/(?<!\\\\u(d[89ab][0-9a-f]{2}))\\\\u(d[c-f][0-9a-f]{2})/i',
        function ($matches) {
            return '';
        },
        $json_data
    );
    
    // Decode the cleaned JSON data
    $data_array = json_decode($json_data, true);
    
    if ($data_array === null && json_last_error() !== JSON_ERROR_NONE) {
        return null;
    }
    
    // Use the data
    
    $allowedTokens = explode(',',$paymentGateway->settings['allowedTokens']);
    $testnetFiltered = array_map(function ($token) use ($wpdb,$allowedTokens) {
      if (strpos($token['key'], 'test') !== false) {
        return null;
      }
      
      $tokenBase = [
        'symbol' => $token['symbol'],
        'contract' => $token['account'],
        'decimals' => $token['supply']['precision'],
        'logo' => $token['metadata']['logo']
      ];
      
      $rawPrices = array_filter($token['pairs'], function ($pair) use ($allowedTokens,$token) {
        return $pair['pair_quote'] == 'USD' && in_array($token['symbol'],$allowedTokens);
      });
      
      $prices = array_values($rawPrices);
      
      if (isset($prices[0])) {
        $mergedToken = array_merge($prices[0], $tokenBase);
        //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching,
        $wpdb->query($wpdb->prepare(
          "INSERT INTO wp_%1s (symbol,contract,token_precision,rate) 
            VALUES (%s,%s,%d,%.12f) 
            ON DUPLICATE KEY UPDATE rate = %.12f",
          XPRCHECKOUT_TABLE_TOKEN_RATES,
          $mergedToken['pair_base'],
          $mergedToken['contract'],
          $mergedToken['decimals'],
          $mergedToken['quote']['price_usd'],
          $mergedToken['quote']['price_usd']
        ));
        
        return $mergedToken;
      }
      
      return null;
    }, $data_array);
      
    return array_values(array_filter($testnetFiltered, function ($token) {
      return !is_null($token);
    }));
      
      } catch (Exception $e) {
        return null;
      }
  }
}


