<?php
/*
 * Plugin Name: Woow - Webauth Gateway for Woocommerce
 * Description: Allow user to pay securly with with multiple crypto currencies through Webauth with NO GAS FEE BABY !
 * Author: Rémy Chauveau AKA Rockerone
 * Author URI: http://5fhc.com
 * Version: 1.0.1
 */

define('PWG_VERSION', '0.0.1');
define('PWG_ROOT_DIR', plugin_dir_path(__FILE__));
define('PWG_ROOT_URL', plugin_dir_url(__FILE__));


include_once PWG_ROOT_DIR . '/includes/woow-gateway.core.php';
function run_proton_wc_gateway()
{

  $plugin = new ProtonWcGateway();
  $plugin->run();
}
run_proton_wc_gateway();
