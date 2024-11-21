<?php
use xprcheckout\utils\OrderResolver;
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_convert_order');

function xprcheckout_register_convert_order()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('xprcheckout/v2', '/order-payment', array(
    'methods'  => 'POST',
    'callback' => 'handle_convert_order',
    'permission_callback' => '__return_true'
  ));
}

function handle_convert_order ($request){
  $params = $request->get_params();

  $returnResult = new WP_Error("order_not_found", "Order not found", [
    'status' => 404
]);

// Validate paymentKey and symbol parameters
if (!isset($params['paymentKey'])) {
    return rest_ensure_response($returnResult);
}



  $args = array(
    'post_type'      => 'shop_order',
    'post_status'    => 'any',
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
    'meta_key'       => '_payment_key', // Meta key for paymentKey
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
    'meta_value'     => $params['paymentKey'],
    'meta_compare'   => '=',
    'posts_per_page' => 1,
);

  $ordersQuery = wc_get_orders($args);
  $existingOrder = $ordersQuery[0] ?? null;
  $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
  $customCurrencyApiKey = $xprcheckoutGateway->get_option('currencyApi');
  $currencyApiKey = empty($customCurrencyApiKey) ? XPRCHECKOUT_PRICE_RATE_API_KEY : $customCurrencyApiKey;
  //TODO allow user to provide API key and add link under the config page field
  $priceRPC = new PriceRateRPC($currencyApiKey);
  $convertedRate = $priceRPC->getUSDConvertionRate(get_woocommerce_currency(), $existingOrder->get_total());
  
  $returnResult = new WP_REST_Response();
  $tokenRPC = new TokenPrices(XPRCHECKOUT_TESTNET_ENDPOINT);
  $tokensPrices = $tokenRPC->getTokenPrices();
  $converted = [];
  foreach ($tokensPrices as $tPrice){
    $converted[] = convertedTokenFactory($tPrice,$convertedRate);
  }
  $network = $existingOrder->get_meta('_network');
  $settlement=OrderResolver::Process($params['paymentKey'],$converted,$network);
  if ($settlement) {
    if ($existingOrder->get_status() !=="completed"){
      $existingOrder->set_status('processing');
    }
  }



  $existingOrder->update_meta_data('_converted_tokens',serialize($converted));
  $existingOrder->update_meta_data('_verified',$settlement->resolved);
  if ($settlement->resolved){
    $existingOrder->update_meta_data('_paid_tokens',$settlement->payment['settlement']);
    $existingOrder->update_meta_data('_buyer_account',$settlement->payment['buyer']);
    $txId = $existingOrder->get_meta('_tx_id');
    if (isset($params['txId']) && empty($txId) ) {
      $existingOrder->update_meta_data('_tx_id',$params['txId']);
    }
    
  }
  $existingOrder->save();  

  

  $baseResponse = new stdClass();
  $baseResponse->usd_amount=$convertedRate;
  $baseResponse->base_currency=get_woocommerce_currency();
  $baseResponse->base_amount=$existingOrder->get_total();
  $baseResponse->converted=[];
  $baseResponse->status=$existingOrder->get_status();
  $baseResponse->converted=$converted;
  $baseResponse->verified = $settlement->resolved;
  

  $returnResult = new WP_REST_Response([
    'status' => 200,
    'response' => "order-payment",
    'body_response' => $baseResponse,
    'nonce' => wp_create_nonce('wp_rest')

  ]);

  return $returnResult;
};

function convertedTokenFactory ($token,$usdAmount){

  $rawConversion = floatval($token['quote']['price_usd']) > 0 ? $usdAmount/$token['quote']['price_usd'] : 0;
  $converted = new stdClass ();
  $converted->symbol=$token['symbol'];
  $converted->amount = toPrecision($rawConversion,$token['decimals'],'ceil',true);
  $converted->logo = $token['logo'];
  $converted->contract = $token['contract'];
  return $converted;       

}