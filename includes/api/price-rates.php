<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_price_rates_routes');

function xprcheckout_register_price_rates_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('xprcheckout/v1', '/price-rates', array(
    'methods'  => 'POST',
    'callback' => 'handle_price_rates',
    'permission_callback' => '__return_true'

  ));
}

function handle_price_rates($request)
{

  $returnResult = new WP_REST_Response();
  $params = $request->get_params();

  $priceRPC = new PriceRateRPC('fca_live_eaf8XPTPKCgoYJwEs0lqrUl2m3HNtUnpyCBj9bqs');
  $convertedRate = $priceRPC->getUSDConvertionRate($params['storeCurrency'], $params['amount']);
  $returnResult = new WP_REST_Response([
    'status' => 200,
    'response' => "prices",
    'body_response' => $convertedRate,
    'nonce' => wp_create_nonce('wp_rest')

  ]);



  return rest_ensure_response($returnResult);
}
