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

  $plugin = new ProtonWcGateway();
  $plugin->run();
}
run_proton_wc_gateway();
