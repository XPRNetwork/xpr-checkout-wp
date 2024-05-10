<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
use wookey\config\Config;
use wookey\i18n\Translations;

add_action('rest_api_init', 'wookey_register_order_routes');

function wookey_register_order_routes()
{
  
  register_rest_route('wookey/v1', '/update-order', array(
    'methods'  => 'POST',
    'callback' => 'handle_get_order',
    'permission_callback' => '__return_true'
  ));
}

function handle_get_order($request)
{
  global $wpdb;
  $params = $request->get_params();
  $returnResult = new WP_Error("order_not_found", "order not validated", [
      'status' => 404
    ]);
  if (!isset($params['paymentKey']) || $params['symbol'])
  $args = array(
    'post_type'      => 'shop_order',
    'post_status'    => 'any',
    'meta_key'       => '_payment_key', // Meta key for paymentKey
    'meta_value'     => $params['paymentKey'],
    'meta_compare'   => '=',
    'posts_per_page' => 1,

  );
  
  $ordersQuery = wc_get_orders($args);
  error_log('in update order');
  error_log(print_r($ordersQuery[0],1));
  $existingOrder = $ordersQuery[0];
  $orderCurrency = $existingOrder->get_currency();
  $sql = "SELECT * FROM wp_".WOOKEY_TABLE_TOKEN_RATES." where symbol = %s";
  $sql = $wpdb->prepare($sql,$params['symbol']);
  $tokens = $wpdb->get_results($sql);
  $existingToken = $tokens[0];

  $sql = "SELECT * FROM wp_".WOOKEY_TABLE_FIAT_RATES." where symbol = %s";
  $sql = $wpdb->prepare($sql,$orderCurrency);
  $currencyRes = $wpdb->get_results($sql);
  $existingCurrency = $currencyRes[0];

  if (!is_null($existingOrder) && !is_null($existingToken) && !is_null($existingCurrency)){
    
    error_log('update order');
    $existingOrder->update_meta_data ('_payment_token',$existingToken->symbol);
    $existingOrder->update_meta_data('_payment_rate',$existingToken->rate);
    $existingOrder->update_meta_data('_fiat_rate',$existingCurrency->rate);
    $existingOrder->save();
    $verifiedPaymentOrder = $existingOrder->get_meta('_verified',true);
    $verifiedPaymentKey = $existingOrder->get_meta('_payment_key',true);
    $verifiedFillRatio = $existingOrder->get_meta('_fill_ratio',true);
    $verifiedTrxId = $existingOrder->get_meta('_tx_id',true);
    $verifiedPayer = $existingOrder->get_meta('_payer',true);
    $returnResult = [
      'paymentKey'=>!empty($verifiedPaymentKey) ? $verifiedPaymentKey : null,
      'transactionId'=>!empty($verifiedTrxId) ? $verifiedTrxId : null,
      'payer'=>!empty($verifiedPayer) ? $verifiedPayer : null,
      'paymentVerified'=>!empty($verifiedPaymentOrder) ? boolval($verifiedPaymentOrder) : false,
      'currency'=>$existingOrder->get_currency(),
      'fillRatio'=>!empty($verifiedFillRatio) ? $verifiedFillRatio : null,
      'status'=>$existingOrder->get_status(),
      'total'=>$existingOrder->get_total(),
      'orderKey'=>$existingOrder->get_order_key(),
      'orderId'=>$existingOrder->get_id(),
    ];

  }

  return rest_ensure_response($returnResult);
}
