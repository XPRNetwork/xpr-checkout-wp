<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
use wookey\config\Config;
use wookey\i18n\Translations;

add_action('rest_api_init', 'wookey_register_cart_routes');

function wookey_register_cart_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('wookey/v1', '/cart', array(
    'methods'  => WP_REST_Server::READABLE,
    'callback' => 'handle_get_cart',
    'permission_callback' => '__return_true'
  ));
}

function handle_get_cart($request)
{

  $returnResult = new \WP_REST_Response([
    'status' => 200,
    'response' => "cart",
    'body_response' => [
      'config' => Config::GetConfigWithCart(),
      'cartTotal' => WC()->cart->total,
      'paymentKey' => WC()->session->get('paymentKey'),
      
    ]

  ]);

  return rest_ensure_response($returnResult);
}
