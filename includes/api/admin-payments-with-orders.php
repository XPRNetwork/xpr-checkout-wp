<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'wookey_register_payments_with_orders_routes');

function wookey_register_payments_with_orders_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('wookey/v1', 'admin/payments', array(
    'methods'  => 'POST',
    'callback' => 'handle_payments_with_orders',
    //'permission_callback' => 'prefix_get_private_data_permissions_check'
    'permission_callback' => '__return_true'

  ));
}

function prefix_get_private_data_permissions_check()
{
  // Restrict endpoint to only users who have the edit_posts capability.
  if (!current_user_can('edit_posts')) {
    return new WP_Error('rest_forbidden', esc_html__('OMG you can not view private data.', 'my-text-domain'), array('status' => 401));
  }

  // This is a black-listing approach. You could alternatively do this via white-listing, by returning false here and changing the permissions check.
  return true;
}


function handle_payments_with_orders($request)
{


  $params = $request->get_params();
  if (!isset($params['actor']) || !isset($params['network'])) {

    return new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
  }
  $rpcEndpoint = $params['network'] == 'testnet' ? WOOKEY_TESTNET_ENDPOINT : WOOKEY_MAINNET_ENDPOINT;
  $protonRPC = new ProtonRPC($rpcEndpoint);
  $payments = $protonRPC->fetchPayments($params['actor']);
  $paymentsWithOrder = [];
  foreach ($payments['rows'] as $row) {
    $args = array(
      'post_type'      => 'shop_order',
      'post_status'    => 'any',
      'meta_key'       => '_payment_key', // Meta key for paymentKey
      'meta_value'     => $row['paymentKey'],
      'meta_compare'   => '=',
      'posts_per_page' => 1,

    );

    $ordersQuery = wc_get_orders($args);
    if ($ordersQuery && isset($ordersQuery[0])) {
      $order = $ordersQuery[0];
      $data = $order->get_data();
      $paymentsWithOrder[] = array_merge($data, $row);
    }
  }

  return rest_ensure_response($paymentsWithOrder);
}
