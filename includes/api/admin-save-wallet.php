<?php
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

add_action('rest_api_init', 'xprcheckout_register_save_wallet_config_routes');

function xprcheckout_register_save_wallet_config_routes()
{
  
  register_rest_route('xprcheckout/v2/admin', '/save-wallet-config', [
    'methods' => 'POST',
    'callback' => 'handle_save_config_request',
    'permission_callback' => 'admin_only_save_wallet_permission_check',
    'args' => [
        'wallets' => [
            'required' => true,
            'validate_callback' => function($param, $request, $key) {
                // Ensure the parameter is an array and validate the required keys and their types
                if (!is_array($param)) {
                    return false;
                }
                
                // Validate testnet configuration
                if (isset($param['testnet'])) {
                    if (!is_array($param['testnet']) ||
                        !isset($param['testnet']['store']) || !is_string($param['testnet']['store']) ||
                        !isset($param['testnet']['verified']) || !is_bool($param['testnet']['verified'])) {
                        return false;
                    }
                }
                
                // Validate mainnet configuration
                if (isset($param['mainnet'])) {
                    if (!is_array($param['mainnet']) ||
                        !isset($param['mainnet']['store']) || !is_string($param['mainnet']['store']) ||
                        !isset($param['mainnet']['verified']) || !is_bool($param['mainnet']['verified'])) {
                        return false;
                    }
                }  
                return true;
            }
        ],
    ],
]);

}

function admin_only_save_wallet_permission_check($request) {
  
  if (!is_user_logged_in()) {
    return new WP_Error('rest_forbidden', __('You must be logged in to access this endpoint.','xprcheckout_webauth_gateway'), ['status' => 403]);
}

// Get the current user
$user = wp_get_current_user();

// Check if the user has the 'administrator' role
if (in_array('administrator', (array) $user->roles, true)) {
    return true;
}

return new WP_Error('rest_forbidden', __('You do not have permission to access this endpoint.','xprcheckout_webauth_gateway'), ['status' => 403]);
}


function handle_save_config_request($request){

  $walletsJson = $request->get_param('wallets');
  $baseResponse = new stdClass();
  
  $serializedWallets = serialize($walletsJson);
  $xprCheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
  $xprCheckoutGateway->update_option('wallets',$serializedWallets);
  $baseResponse->saved=true;
  
  
  $returnResult = new WP_REST_Response([
      'status' => 200,
      'response' => "order-payment",
      'body_response' => $baseResponse,
      'nonce' => wp_create_nonce('wp_rest')
  
    ]);

  return $returnResult;


}


