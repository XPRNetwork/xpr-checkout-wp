<?php
use xprcheckout\utils\OrderResolver;
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_transaction_verification_routes');

function xprcheckout_register_transaction_verification_routes()
{
  // register_rest_route() handles more arguments but we are going to stick to the basics for now.
  register_rest_route('xprcheckout/v1', '/verify-transaction', array(
    'methods'  => 'POST',
    'callback' => 'handle_transaction_check',
    'permission_callback' => '__return_true'
  ));
}

function handle_transaction_check($request)
{

  global $wpdb;
  $returnResult = new WP_REST_Response();
  $params = $request->get_params();
  if (!isset($params['paymentKey']) || !isset($params['network'])) {

    return new WP_REST_Response([
      'status' => 403,
      'response' => "Unauthorized",
      'body_response' => null
    ]);
  }
  $actor = null;
  if (isset($params['actor'])){
    $actor = $params['actor'];
  }
  $resolved = OrderResolver::Process(($params['paymentKey']),$params['network'],$actor);
  if (is_null($resolved)) return rest_ensure_response($returnResult);
  $returnResult = new WP_REST_Response([
    'status' => 200,
    'response' => "done",
    'body_response' => $resolved
  ]);
    
  return rest_ensure_response($returnResult);
  
}
