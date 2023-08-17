<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'wookey_register_cart_routes');

function wookey_register_cart_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('wookey/v1', '/cart', array(
    'methods'  => 'GET',
    'callback' => 'handle_get_cart',
    'permission_callback' => '__return_true'

  ));
}

function handle_get_cart($request)
{




  $returnResult = new WP_REST_Response([
    'status' => 200,
    'response' => "prices",
    'body_response' => [
      'checkout' => WC()->customer,
      'amount' => WC()->cart->total,
      'paymentKey' => WC()->session->get('paymentKey'),
    ]

  ]);

  return rest_ensure_response($returnResult);
}
