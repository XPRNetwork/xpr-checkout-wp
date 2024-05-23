<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_payment_verification_routes');

function xprcheckout_register_payment_verification_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('xprcheckout/v1', '/verify-payment', array(
    'methods'  => 'POST',
    'callback' => 'handle_payment_check',
    'permission_callback' => '__return_true'
  ));
}

function handle_payment_check($request)
{

  $returnResult = new WP_REST_Response();
  $params = $request->get_params();
  if (!isset($params['paymentKey']) || !isset($params['network'])) {

    return new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
  }

  $params = $request->get_params();
  $args = array(
    'post_type'      => 'shop_order',
    'post_status'    => 'any',
    'meta_key'       => '_paymentKey', // Meta key for paymentKey
    'meta_value'     => $params['paymentKey'],
    'meta_compare'   => '=',
    'posts_per_page' => 1,

  );
  $ordersQuery = wc_get_orders($args);
  $returnResult = new WP_Error("order_not_found", "order not validated", [
    'status' => 404
  ]);

  $existingOrder = $ordersQuery[0];
  if (!isset($existingOrder) ) return rest_ensure_response($returnResult);
  $orderPaymentKey = $existingOrder->get_meta('_paymentKey');
  if ($orderPaymentKey != $params['paymentKey'] ) return rest_ensure_response($returnResult);

  error_log("##PAYMENT KEY MATCH PROVIDED");
  error_log(print_r($paymentKey, 1));
  error_log(print_r($params['paymentKey'], 1));
  error_log("##Have user id");
  error_log(print_r(get_current_user_id(), 1));


  $rpcEndpoint = $params['network'] == 'testnet' ? XPRCHECKOUT_TESTNET_ENDPOINT : XPRCHECKOUT_MAINNET_ENDPOINT;
  $rpc = new ProtonRPC($rpcEndpoint);
  $isTransactionVerified = $rpc->verifyTransaction($params['transactionId'], $params['paymentKey']);
  if (!$isTransactionVerified) {
    $returnResult = new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
    return rest_ensure_response($returnResult);
  }

  WC()->session->set('transactionId', $params['transactionId']);
  $rpcResults = $rpc->verifyPaymentStatusByKey($params['paymentKey']);

  if ($rpcResults) {

    $order->payment_complete(); 
    $returnResult = new WP_REST_Response([
      'status' => 200,
      'response' => "order validated",
      'body_response' => [
        "validated" => true,
        "paymentKey" => $params['paymentKey'],
        "transactionId" => $params['transactionId'],

      ]
    ]);
    return rest_ensure_response($returnResult);
  } else {
    $returnResult = new WP_Error("order_not_found", "order not validated", [
      'status' => 404
    ]);
    return rest_ensure_response($returnResult);
  }
}
