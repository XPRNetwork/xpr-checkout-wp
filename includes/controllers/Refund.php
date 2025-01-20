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

    if($this->isCurrentScreenIsOrderEdit()){
      global $post;
      $order = wc_get_order($post->ID);
      wp_enqueue_style('xprcheckout_admin_refund_style', XPRCHECKOUT_ROOT_URL . 'dist/refund/build/app.css?v=' . uniqid(),[], time());
      wp_register_script_module('xprcheckout_admin_refund', XPRCHECKOUT_ROOT_URL . 'dist/refund/build/app.js?v=' . uniqid(), [], time());
      
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
      __('XPRCheckout payment', 'xprcheckout-webauth-gateway-for-woocommerce'),
      [$this, 'renderMetabox'],
      'woocommerce_page_wc-orders',
      'advanced',
      'core'

    );
    add_meta_box(
      'woocommerce-xprcheckout-payment',
      __('XPRCheckout payment', 'xprcheckout-webauth-gateway-for-woocommerce'),
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
<?php
$adminConfig = Config::GetAdminConfig();
$baseConfig = Config::GetBaseConfig();
$baseConfig['amountToRefund'] = $order->get_meta('_paid_tokens', true);
$baseConfig['accountToRefund'] = $order->get_meta('_buyer_account', true);
$baseConfig['requestedPaymentKey'] = $order->get_meta('_payment_key', true);
$baseConfig['orderStatus'] = $order->get_status();

wp_localize_script('xprcheckout-refund-config', 'xprcheckoutConfig', array_merge($baseConfig, $adminConfig));
wp_enqueue_script('xprcheckout-refund-config', XPRCHECKOUT_ROOT_URL . 'assets/js/xprcheckout-config.js', array(), XPRCHECKOUT_VERSION, true);
?>
  <?php 
    $transactionId = $order->get_meta('_tx_id');
    $network = $order->get_meta('_network');
    $amount = $order->get_meta('_paid_tokens');
    $color = $network == "mainnet" ? "#7cc67c" : "#f1dd06";
    $link = $network == "mainnet" ? "https://explorer.xprnetwork.org/transaction/" : "https://testnet.explorer.xprnetwork.org/transaction/";
    ?>
    <div style="display:grid;grid-template-columns:1fr;gap:5px">
      <div>
        <h4>Tokens paid</h4>
        <?php 
          
          
          if ($order->get_payment_method() == "xprcheckout") {
            echo '<span style="font-weight:bold">' . esc_attr($amount) . '</span>';
          } else {
            echo '';
          }
        ?>
      </div>
      <div>
        <h4>View Transaction <?php echo esc_attr($network) ?></h4>
        <?php 
          
          
          if ($order->get_payment_method() == "xprcheckout") {
            echo '<a class="button-primary" style="width:100%;color:#ffffff;background-color:' . esc_attr($color) . ';" target="_blank" href="' . esc_attr($link) . esc_attr($transactionId) . '">' . esc_attr(substr($transactionId, strlen($transactionId) - 8, strlen($transactionId))) . '</a>';
          } else {
            echo '';
          }
        ?>
      </div>
      <div id="xpr-refund"></div>
    </div>
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

  public function isCurrentScreenIsOrderEdit (){

    global $current_screen;
    if (!isset($current_screen)) return false;
    switch ($current_screen->id) {
      case 'woocommerce_page_wc-orders':
        return true;
      case 'shop_order':
        return true;
    }
   
  }
}
