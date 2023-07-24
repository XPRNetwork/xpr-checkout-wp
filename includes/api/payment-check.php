<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'woow_register_payment_verification_routes');

function woow_register_payment_verification_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('woow/v1', '/verify-payment', array(
    'methods'  => 'POST',
    'callback' => 'handle_payment_check',
    'permission_callback' => '__return_true'

  ));
}

function handle_payment_check($request)
{

  $returnResult = new WP_REST_Response();
  $params = $request->get_params();
  if (isset($params['paymentKey']) && isset($params['transactionId'])) {

    $args = array(
      'post_type'      => 'shop_order',
      'post_status'    => 'any',
      'meta_key'       => '_paymentKey', // Meta key for paymentKey
      'meta_value'     => $params['paymentKey'],
      'meta_compare'   => '=',
      'posts_per_page' => 1,
    );

    $ordersQuery = wc_get_orders($args);
    $orderData  = null;

    if (count($ordersQuery) > 0) {
      $order = $ordersQuery[0]; // Return the first order found

      //TODO: Change the endpoint according to $params['network']
      $rpc = new ProtonRPC("https://test.proton.eosusa.io");

      $isTransactionVerified = $rpc->verifyTransaction($params['transactionId'], $params['paymentKey']);
      if (!$isTransactionVerified) {
        $returnResult = new WP_REST_Response([
          'status' => 403,
          'response' => "Unauthorized",
          'body_response' => null
        ]);
        return rest_ensure_response($returnResult);
      }

      $rpcResults = $rpc->verifyPaymentStatusByKey("woow", "woow", "payment", $params['paymentKey'], 100);

      if ($rpcResults) {

        $order->update_meta_data('_txId', $params['transactionId']);
        $order->update_meta_data('_net', $params['network']);
        $order->update_status('completed');
        $order->save();
        $orderData = $order->get_data();
        $returnResult = new WP_REST_Response([
          'status' => 200,
          'response' => "order validated",
          'body_response' => $orderData
        ]);
      } else {
        $returnResult = new WP_Error("order_not_found", "order not validated", [

          'status' => 404
        ]);
      }
    } else {

      $returnResult = new WP_REST_Response([
        'status' => 404,
        'response' => "order not found",
        'body_response' => null
      ]);
    }
  } else {

    $returnResult = new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
  }



  return rest_ensure_response($returnResult);
}
