<?php

namespace wookey\gateway;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Handles the integration of the Wookey payment gateway within WooCommerce.
 * 
 * This class manages the registration and initialization of the Wookey payment gateway 
 * for WooCommerce, including registering necessary actions and filters.
 *
 * @package WooCommerce\Classes\Gateway
 */
class GatewayWrapper
{
  /**
   * GatewayWrapper constructor.
   * 
   * Initializes the gateway wrapper and registers necessary actions and filters.
   */
  public function __construct()
  {

    $this->registerFilters();
    $this->registerActions();
  }

  /**
   * Registers WooCommerce filters.
   * 
   * Attaches methods to WooCommerce filter hooks.
   * @access private
   */
  private function registerFilters()
  {
    add_filter('woocommerce_payment_gateways', [$this, 'registerGatewayClass']);
  }

  /**
   * Registers the Wookey payment gateway within WooCommerce.
   * 
   * @param array $gateways List of available WooCommerce payment gateways.
   * @return array List of payment gateways with the Wookey gateway added.
   */
  public function registerGatewayClass($gateways)
  {

    $gateways[] = 'WC_WookeyGateway';
    return $gateways;
  }

  /**
   * Registers WordPress and WooCommerce actions.
   * 
   * Attaches methods to WordPress and WooCommerce action hooks.
   * @access private
   */
  private function registerActions()
  {
    add_action('wp_loaded', [$this, 'forceLoadCart'], 5);
    add_action('plugins_loaded', [$this, 'initGatewayClass']);
  }
  /**
   * Forces WooCommerce to load the cart under certain conditions.
   * 
   * Specifically, if the WooCommerce version is 3.6.0 or greater and the request is a REST API request, 
   * this method ensures the cart is loaded and available.
   */
  public function forceLoadCart()
  {
    if (version_compare(WC_VERSION, '3.6.0', '>=') && WC()->is_rest_api_request()) {


      require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
      require_once WC_ABSPATH . 'includes/wc-notice-functions.php';

      if (null === WC()->session) {
        $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler'); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

        // Prefix session class with global namespace if not already namespaced
        if (false === strpos($session_class, '\\')) {
          $session_class = '\\' . $session_class;
        }

        WC()->session = new $session_class();
        WC()->session->init();
      }

      /**
       * For logged in customers, pull data from their account rather than the
       * session which may contain incomplete data.
       */
      if (is_null(WC()->customer)) {
        if (is_user_logged_in()) {
          WC()->customer = new \WC_Customer(get_current_user_id());
        } else {
          WC()->customer = new \WC_Customer(get_current_user_id(), true);
        }

        // Customer should be saved during shutdown.
        add_action('shutdown', array(WC()->customer, 'save'), 10);
      }

      // Load Cart.
      if (null === WC()->cart) {
        WC()->cart = new \WC_Cart();
      }
    }
  }

  /**
   * Initializes the Wookey payment gateway class.
   * 
   * Requires the necessary file to make the Wookey gateway class available.
   */
  function initGatewayClass()
  {
    require_once WOOKEY_ROOT_DIR . 'includes/woocommerce/gateway/wookey-gateway.php';
  }
}
