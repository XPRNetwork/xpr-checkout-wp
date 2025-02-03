<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_payments_with_orders_routes');

function xprcheckout_register_payments_with_orders_routes()
{
  
  register_rest_route('xprcheckout/v2/admin', '/refund', [
    'methods' => 'POST',
    'callback' => 'xprcheckout_refund_request_handler',
    'permission_callback' => 'xprcheckout_admin_only_permission_check',
    'args' => [
        'paymentKey' => [
            'required' => true,
            'validate_callback' => function($param, $request, $key) {
                return is_string($param);
            }
        ],
    ],
]);
}

function xprcheckout_admin_only_permission_check($request) {
  
  if (!is_user_logged_in()) {
    return new WP_Error('rest_forbidden', __('You must be logged in to access this endpoint.','xprcheckout-webauth-gateway-for-e-commerce'), ['status' => 403]);
}

// Get the current user
$user = wp_get_current_user();

// Check if the user has the 'administrator' role
if (in_array('administrator', (array) $user->roles, true)) {
    return true;
}

return new WP_Error('rest_forbidden', __('You do not have permission to access this endpoint.','xprcheckout-webauth-gateway-for-e-commerce'), ['status' => 403]);
}


function xprcheckout_refund_request_handler($request)
{


    $paymentKey = sanitize_text_field($request->get_param('paymentKey'));
    

    $baseResponse = new stdClass();
    $baseResponse->refunded=false;
    
    $order = xprcheckout_get_order_by_payment_key($paymentKey);
    if(!is_null($order)){
        $order->set_status('refunded');
        $order->save();
        $baseResponse->refunded=true;
    }
    
    $returnResult = new WP_REST_Response([
        'status' => 200,
        'response' => "order-payment",
        'body_response' => $baseResponse,
        'nonce' => wp_create_nonce('wp_rest')
    
      ]);
  
      return $returnResult;


}


