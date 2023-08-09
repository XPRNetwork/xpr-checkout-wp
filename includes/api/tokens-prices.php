<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'wookey_register_tokens_prices_routes');

function wookey_register_tokens_prices_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('wookey/v1', '/tokens-prices', array(
    'methods'  => 'POST',
    'callback' => 'handle_tokens_prices',
    'permission_callback' => '__return_true'

  ));
}

function handle_tokens_prices($request)
{

  $returnResult = new WP_REST_Response();
  $tokenRPC = new TokenPrices();
  $tokensPrices = $tokenRPC->getTokenPrices();
  $returnResult = new WP_REST_Response([
    'status' => 200,
    'response' => "prices",
    'body_response' => $tokensPrices

  ]);

  return rest_ensure_response($returnResult);
}
