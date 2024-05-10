<?php

namespace wookey\admin;

use wookey\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
/**
 * Admin Refund Handler for Wookey Payment Gateway.
 *
 * Manages the custom refund functionality, styles, and scripts related to the Wookey payment method in the WooCommerce backend.
 * 
 */

class Refund
{

  /**
   * Refund constructor.
   * 
   * Initializes the refund handler by registering necessary actions.
   */
  public function __construct()
  {

    $this->registerActions();
    $this->registerFilters();
  }

  /**
   * Registers WordPress actions.
   * 
   * Attaches methods to WordPress action hooks.
   * @access private
   */
  private function registerActions()
  {

    add_action('admin_enqueue_scripts', [$this, 'registerScript']);
    add_action('add_meta_boxes', [$this, 'registerMetabox']);
  }

  /**
   * Enqueues the necessary scripts and styles when on the WooCommerce order screen.
   * 
   * The scripts are only loaded for orders with Wookey as the payment method.
   */
  public function registerScript()
  {

    global $current_screen;
    if (isset($current_screen) && $current_screen->id == 'shop_order') {
      global $post;
      $order = wc_get_order($post->ID);
      wp_enqueue_style('wookey_admin_refund_style', WOOKEY_ROOT_URL . 'dist/admin/refund/wookey.admin.refund.css?v=' . uniqid());
      wp_register_script('wookey_admin_refund', WOOKEY_ROOT_URL . 'dist/admin/refund/wookey.admin.refund.iife.js?v=' . uniqid(), [], time(), true);
      wp_localize_script('wookey_admin_refund', 'wookeyRefundParams', Config::GetConfigWithOrderById($order->get_id()));
      wp_enqueue_script('wookey_admin_refund');
    };
  }

  /**
   * Registers a new meta box for displaying Wookey payment information on the WooCommerce order screen.
   * 
   * The meta box is only displayed for orders with Wookey as the payment method.
   */
  public function registerMetabox()
  {
    add_meta_box(
      'woocommerce-wookey-payment',
      __('Wookey payment', 'wookey'),
      [$this, 'renderMetabox'],
      'shop_order',
      'advanced',
      'core'

    );
  }
  /**
   * Renders the content of the Wookey payment meta box on the WooCommerce order screen.
   * 
   * @param WP_Post $post The post object of the current post screen.
   */
  public function renderMetabox($post)
  {

    $order = wc_get_order($post->ID);
    if ($order->get_payment_method() !== "wookey") return;

?>
    <div id="wookey-refund"></div>
<?php

  }

  /**
   * Registers WordPress filters.
   * 
   * Attaches methods to WordPress filter hooks.
   * @access private
   */
  private function registerFilters()
  {
    add_filter('woocommerce_admin_order_should_render_refunds', [$this, 'disableDefaultRefund'], 99, 2);
  }

  /**
   * Disables the default WooCommerce refund functionality for Wookey payment orders.
   * 
   * @param bool $enableRefund Whether to enable the default WooCommerce refund functionality.
   * @param WC_Order $order The WooCommerce order object.
   * @return bool Whether to enable the default WooCommerce refund functionality.
   */
  public function disableDefaultRefund($enableRefund, $order)
  {
    $order = wc_get_order($order);
    if ($order->get_payment_method() == "wookey") return false;
    return $enableRefund;
  }
}
