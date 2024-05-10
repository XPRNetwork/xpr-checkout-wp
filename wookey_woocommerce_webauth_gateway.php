<?php
/*
 * Plugin Name: WebAuthPay - WebAuth Gateway for Woocommerce
 * Description: Allow user to pay securly with with multiple crypto currencies through Webauth with NO GAS FEE BABY !
 * Author: Rémy Chauveau AKA Rockerone
 * Author URI: hypersolid.io
 * Version: 1.1.0
 * Text Domain: wookey
 * Domain Path: /i18n/languages/
 */

define('WOOKEY_VERSION', '1.1.0');
define('WOOKEY_ROOT_DIR', plugin_dir_path(__FILE__));
define('WOOKEY_ROOT_URL', plugin_dir_url(__FILE__));
define('WOOKEY_MAINNET_ENDPOINT', "https://proton.eosusa.io");
define('WOOKEY_TESTNET_ENDPOINT', "https://test.proton.eosusa.io");
define('WOOKEY_MAINNET_BLOCK_EXPLORER', "https://protonscan.io");
define('WOOKEY_TESTNET_BLOCK_EXPLORER', "https://testnet.protonscan.io");
define('WOOKEY_TABLE_TOKEN_RATES', "token_rates");
define('WOOKEY_TABLE_FIAT_RATES', "fiat_rates");



function wookey_install(){

  global $wpdb;
	global $jal_db_version;

	$tokenTableName = $wpdb->prefix . WOOKEY_TABLE_TOKEN_RATES;
	$fiatTableName = $wpdb->prefix . WOOKEY_TABLE_FIAT_RATES;
	
	$charset_collate = $wpdb->get_charset_collate();

	$tokenRatesSql = "CREATE TABLE $tokenTableName (
    symbol tinytext NOT NULL,
    contract text NOT NULL,
    token_precision int DEFAULT 0 NOT NULL,
    rate float DEFAULT 0 NOT NULL,
    updated datetime DEFAULT NOW() NOT NULL,
    PRIMARY KEY (symbol(12))
	) $charset_collate;";
	
  $fiatRatesSql = "CREATE TABLE $fiatTableName (
    symbol tinytext NOT NULL,
    rate float DEFAULT 0 NOT NULL,
    updated datetime DEFAULT NOW() NOT NULL,
    PRIMARY KEY (symbol(12))
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $tokenRatesSql );
	dbDelta( $fiatRatesSql );

	add_option( 'wookey_db_version', WOOKEY_VERSION );

}
register_activation_hook( __FILE__, 'wookey_install' );





include_once WOOKEY_ROOT_DIR . '/includes/wookey-gateway.core.php';
function run_proton_wc_gateway()
{

  if ( class_exists( 'WooCommerce' ) ) {
    $plugin = new ProtonWcGateway();
    $plugin->run();
  }else {
    add_action( 'admin_notices', 'sample_admin_notice_success' );
    
  }
}

function sample_admin_notice_success() {
  ?>
  <div  class="notice notice-error">
      <p><b><?php _e( 'Wookey - Webauth Gateway for Woocommerce require WooCommerce to work!', 'sample-text-domain' ); ?></b></p>
      <a href="/wp-admin/plugin-install.php?s=woo&tab=search&type=term">Install Woocommerce </a>
      <p></p>
  </div>
  <?php
}

function wookey_register_endpoint (){

    
  global $wp_rewrite;
  add_rewrite_endpoint('wookey', EP_ROOT, 'wookey' );
  add_rewrite_endpoint('payments', EP_PERMALINK, 'paymentKey' );
  add_rewrite_rule(
    'wookey/payments/(([a-z0-9])*)/?$',
    'index.php?wookey=payments&paymentKey=$matches[1]',
    'top'
);
  
  $wp_rewrite->flush_rules(true);
  
}
add_action( 'init', 'wookey_register_endpoint' );

function wookey_register_query_vars($vars){
  $vars[] = 'paymentKey';
	return $vars;
}
add_filter( 'query_vars', 'wookey_register_query_vars' );

function wookey_template_redirect ($template){

  global $wp_query;
  $mutatedTemplate = $template;
  if (isset($wp_query->query_vars['wookey'])){
    if($wp_query->query_vars['wookey'] == "payments"){
      $filePath = WOOKEY_ROOT_DIR.'includes/templates/template-payments.php';
      $fileEx = file_exists($filePath);
      if ($fileEx){
        
        $mutatedTemplate = $filePath;
      }
    }
  }
  return $mutatedTemplate;
  
}
add_filter( 'template_include', 'wookey_template_redirect',99 );

function wookey_redirect_to_payment (){

  global $wp_query;
  if( is_wc_endpoint_url( 'order-received' )) {
      if(isset($wp_query->query_vars['order-received'])){

        $orderId = $wp_query->query_vars['order-received'];
        $order = wc_get_order($orderId);
        $paymentKey = $order->get_meta('_payment_key');
        error_log('the existing payment key'.$paymentKey);
        error_log($order->get_payment_method());
        if ($order->get_payment_method() == "wookey"){

          if (empty($paymentKey)){
            
            $serializedOrder = wp_json_encode($order);
            $paymentKey =  hash('sha256', $serializedOrder . time());
            $order->update_meta_data('_payment_key', $paymentKey);
            
          }
          $order->set_status('pending');
          $order->set_date_modified( time() );
          $order->save();
          wp_redirect(home_url('/wookey/payments/'.$paymentKey));
          exit;
        }
      }
      
    }

}
add_action( 'template_redirect', 'wookey_redirect_to_payment' );

run_proton_wc_gateway();
