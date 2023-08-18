<?php

namespace wookey\admin;

use wookey\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Admin Store Registration Handler for Wookey Payment Gateway.
 *
 * Manages the custom scripts and styles related to store registration in the WooCommerce backend for Wookey.
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
   * The scripts are meant to assist in the configuration of Wookey-specific settings.
   */
  private function registerActions()
  {
    add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
  }

  /**
   * Enqueues the necessary scripts and styles for store registration on the WooCommerce settings screen.
   * 
   * The scripts are meant to assist in the configuration of Wookey-specific settings.
   */
  public function registerScripts()
  {

    global $current_screen;
    $baseConfig = Config::GetBaseConfig();
    $extendedConfig = [
      "networkCheckBoxSelector" => "#woocommerce_wookey_testnet",
      "mainnetAccountFieldSelector" => "#woocommerce_wookey_mainwallet",
      "testnetAccountFieldSelector" => "#woocommerce_wookey_testwallet",
    ];
    if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wc-settings') {
      wp_register_script('wookey_admin_regstore', WOOKEY_ROOT_URL . 'dist/admin/regstore/wookey.admin.regstore.iife.js?v=' . uniqid(), [], time(), true);
      wp_localize_script('wookey_admin_regstore', 'wookeyRegStoreParams', array_merge($baseConfig, $extendedConfig));
      wp_enqueue_script('wookey_admin_regstore');
      wp_enqueue_style('wookey_admin_regstore_style', WOOKEY_ROOT_URL . 'dist/admin/regstore/wookey.admin.regstore.css?v=' . uniqid());
    };
  }
}
