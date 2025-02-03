<?php

/*
==================================================
Public route that fetch public data in order to 
check on-chain order settlement status.
==================================================
*/

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
use xprcheckout\utils\OrderResolver;
add_action('rest_api_init', 'xprcheckout_register_transaction_verification_routes');

function xprcheckout_register_transaction_verification_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('xprcheckout/v1', '/verify-settlement', array(
    'methods'  => 'POST',
    'callback' => 'xprcheckout_transaction_verification_handler',
    'permission_callback' => 'xprcheckout_transaction_verification_permission_check'
  ));
}

function xprcheckout_transaction_verification_permission_check($request) {
  // Convert __return_true to a true handler, but it remain a public route.
  if (is_user_logged_in()) {
      return true;
  }
  return true;
}

function xprcheckout_transaction_verification_handler($request)
{

  global $wpdb;
  $returnResult = new WP_REST_Response();
  $params = $request->get_params();
  if (!isset($params['paymentKey'])) {

    return new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
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
  if (is_null($existingOrder)) {
    return new WP_REST_Response([
      'status' => 404,
      'response' => "Order not found",
      'body_response' => null
    ]);
  } 
  $network = $existingOrder->get_meta('_network');
  $rawConvertedTokens = $existingOrder->get_meta('_converted_tokens');
  if (is_null($rawConvertedTokens)){
    return new WP_REST_Response([
      'status' => 404,
      'response' => "Order not found",
      'body_response' => null
    ]);
  }

  $convertedTokens = unserialize($rawConvertedTokens);
  
  $resolved = OrderResolver::Process($params['paymentKey'],$convertedTokens,$network);
  $existingOrder->update_meta_data('_verified',$resolved);
  
  if ($resolved) {
    if ($existingOrder->get_status() !=="completed"){
      $existingOrder->set_status('processing');
    }
  }
  $existingOrder->save();
  $baseResponse = new stdClass();
  $baseResponse->verified = $resolved;
  $baseResponse->orderStatus = $existingOrder->get_status();
  
  return new WP_REST_Response([
    'status' => 200,
    'response' => "order verify",
    'body_response' => $baseResponse
  ]);
  
  
}
