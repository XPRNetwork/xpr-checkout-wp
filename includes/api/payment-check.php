<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'wookey_register_payment_verification_routes');

function wookey_register_payment_verification_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('wookey/v1', '/verify-payment', array(
    'methods'  => 'POST',
    'callback' => 'handle_payment_check',
    'permission_callback' => '__return_true'

  ));
}

function handle_payment_check($request)
{

  $returnResult = new WP_REST_Response();
  $params = $request->get_params();
  if (!isset($params['paymentKey']) || !isset($params['transactionId']) || !isset($params['network'])) {

    return new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
  }


  $cart = WC()->cart;

  if (is_null($cart)) {

    return rest_ensure_response($returnResult);
  }
  error_log("## HAS CART");
  error_log(print_r($cart->get_cart_hash(), 1));
  $paymentKey = WC()->session->get('paymentKey');

  if (is_null($paymentKey)) {

    return rest_ensure_response($returnResult);
  }

  error_log("## HAS PAYMENT KEY");
  error_log(print_r($paymentKey, 1));

  if ($paymentKey != $params['paymentKey']) {
    return rest_ensure_response($returnResult);
  }

  error_log("##PAYMENT KEY MATCH PROVIDED");
  error_log(print_r($paymentKey, 1));
  error_log(print_r($params['paymentKey'], 1));

  $rpcEndpoint = $params['network'] == 'testnet' ? WOOKEY_TESTNET_ENDPOINT : WOOKEY_MAINNET_ENDPOINT;
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
