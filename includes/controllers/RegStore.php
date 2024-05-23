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
      "mainnetAccountFieldSelector" => "#woocommerce_xprcheckout_mainwallet",
      "testnetAccountFieldSelector" => "#woocommerce_xprcheckout_testwallet",
    ];
    
    if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wc-settings') {
      wp_register_script('xprcheckout_admin_regstore', XPRCHECKOUT_ROOT_URL . 'dist/admin/regstore/xprcheckout.admin.regstore.iife.js?v=' . uniqid(), [], time(), true);
      wp_localize_script('xprcheckout_admin_regstore', 'xprcheckoutRegStoreParams', array_merge($baseConfig, $extendedConfig));
      wp_enqueue_script('xprcheckout_admin_regstore');
      wp_enqueue_style('xprcheckout_admin_regstore_style', XPRCHECKOUT_ROOT_URL . 'dist/admin/regstore/xprcheckout.admin.regstore.css?v=' . uniqid());
    };
  }
}
