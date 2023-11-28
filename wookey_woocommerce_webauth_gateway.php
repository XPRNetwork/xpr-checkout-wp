<?php
/*
 * Plugin Name: Wookey - Webauth Gateway for Woocommerce
 * Description: Allow user to pay securly with with multiple crypto currencies through Webauth with NO GAS FEE BABY !
 * Author: Rémy Chauveau AKA Rockerone
 * Author URI: hypersolid.io
 * Version: 1.0.7-beta
 * Text Domain: wookey
 * Domain Path: /i18n/languages/
 */

define('WOOKEY_VERSION', '1.0.7-beta');
define('WOOKEY_ROOT_DIR', plugin_dir_path(__FILE__));
define('WOOKEY_ROOT_URL', plugin_dir_url(__FILE__));
define('WOOKEY_MAINNET_ENDPOINT', "https://proton.eosusa.io");
define('WOOKEY_TESTNET_ENDPOINT', "https://test.proton.eosusa.io");
define('WOOKEY_MAINNET_BLOCK_EXPLORER', "https://protonscan.io");
define('WOOKEY_TESTNET_BLOCK_EXPLORER', "https://testnet.protonscan.io");
error_log(WOOKEY_ROOT_DIR . 'includes/controllers/Cart.php');





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


run_proton_wc_gateway();
