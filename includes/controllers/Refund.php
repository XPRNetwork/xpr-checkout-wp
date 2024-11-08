<?php

namespace xprcheckout\admin;

use xprcheckout\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
/**
 * Admin Refund Handler for XPRCheckout Payment Gateway.
 *
 * Manages the custom refund functionality, styles, and scripts related to the XPRCheckout payment method in the WooCommerce backend.
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
   * The scripts are only loaded for orders with XPRCheckout as the payment method.
   */
  public function registerScript()
  {

    global $current_screen;
    if (isset($current_screen) && $current_screen->id == 'shop_order') {
      global $post;
      $order = wc_get_order($post->ID);
      wp_enqueue_style('xprcheckout_admin_refund_style', XPRCHECKOUT_ROOT_URL . 'dist/refund/static/css/app.css?v=' . uniqid(),[], time());
      wp_register_script_module('xprcheckout_admin_refund', XPRCHECKOUT_ROOT_URL . 'dist/refund/static/js/app.js?v=' . uniqid(), [], time());
      
      wp_enqueue_script_module('xprcheckout_admin_refund');
    };
  }

  /**
   * Registers a new meta box for displaying XPRCheckout payment information on the WooCommerce order screen.
   * 
   * The meta box is only displayed for orders with XPRCheckout as the payment method.
   */
  public function registerMetabox()
  {
    add_meta_box(
      'woocommerce-xprcheckout-payment',
      __('XPRCheckout payment', 'xprcheckout'),
      [$this, 'renderMetabox'],
      'shop_order',
      'advanced',
      'core'

    );
  }
  /**
   * Renders the content of the XPRCheckout payment meta box on the WooCommerce order screen.
   * 
   * @param WP_Post $post The post object of the current post screen.
   */
  public function renderMetabox($post)
  {

    $order = wc_get_order($post->ID);
    if ($order->get_payment_method() !== "xprcheckout") return;

?>
<script>
        <?php 
          $adminConfig =Config::GetAdminConfig(); 
          $baseConfig = Config::GetBaseConfig();
          $baseConfig['amountToRefund']= $order->get_meta('_paid_tokens',true);
          $baseConfig['accountToRefund']= $order->get_meta('_buyer_account',true);
          $baseConfig['requestedPaymentKey']= $order->get_meta('_payment_key',true);
          $baseConfig['orderStatus']= $order->get_status();
          $baseConfig['orderStatus']= $order->get_status();
          ?>
          window.pluginConfig = <?php echo json_encode(array_merge($baseConfig,$adminConfig)); ?>;
      </script>
    <div id="refund"></div>
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
   * Disables the default WooCommerce refund functionality for XPRCheckout payment orders.
   * 
   * @param bool $enableRefund Whether to enable the default WooCommerce refund functionality.
   * @param WC_Order $order The WooCommerce order object.
   * @return bool Whether to enable the default WooCommerce refund functionality.
   */
  public function disableDefaultRefund($enableRefund, $order)
  {
    $order = wc_get_order($order);
    if ($order->get_payment_method() == "xprcheckout") return false;
    return $enableRefund;
  }
}
