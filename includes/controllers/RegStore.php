<?php

namespace xprcheckout\admin;

use xprcheckout\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Admin Store Registration Handler for XPRCheckout Payment Gateway.
 *
 * Manages the custom scripts and styles related to store registration in the WooCommerce backend for XPRCheckout.
 * 
 * @package WooCommerce\Classes\Admin
 */
class RegStore
{

  /**
   * RegStore constructor.
   * 
   * Initializes the store registration handler by registering necessary actions.
   */
  public function __construct()
  {

    $this->registerActions();
  }

  /**
   * Enqueues the necessary scripts and styles for store registration on the WooCommerce settings screen.
   * 
   * The scripts are meant to assist in the configuration of XPRCheckout-specific settings.
   */
  private function registerActions()
  {
    
    add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
  }

  /**
   * Enqueues the necessary scripts and styles for store registration on the WooCommerce settings screen.
   * 
   * The scripts are meant to assist in the configuration of XPRCheckout-specific settings.
   */
  public function registerScripts()
  {

    global $current_screen;
    $baseConfig = Config::GetDashbordConfig();
    $extendedConfig = [
      "networkSelector" => "#woocommerce_xprcheckout_network",
      "mainnetAccountFieldSelector" => "#woocommerce_xprcheckout_wallet",
      
    ];
    
    if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wc-settings') {
      wp_register_script_module('xprcheckout_admin_regstore', XPRCHECKOUT_ROOT_URL . 'dist/regstore/static/js/app.js?v=' . uniqid(), [], time());
      wp_enqueue_script_module('xprcheckout_admin_regstore');
      wp_enqueue_style('xprcheckout_admin_regstore_style', XPRCHECKOUT_ROOT_URL . 'dist/regstore/static/css/app.css?v=' . uniqid(),[], time());
    };
  }
}
