<?php

namespace xprcheckout\admin;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


/**
 * Admin Orders Handler.
 *
 * Enhances the WooCommerce orders table in the admin dashboard by adding 
 * columns for transaction IDs and network (mainnet/testnet) from the XPRCheckout payment gateway.
 * 
 */

class Orders
{

  /**
   * Orders constructor.
   * 
   * Initializes the orders handler by registering necessary actions and filters.
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

    add_action('manage_shop_order_posts_custom_column', [$this, 'mutateOrdersRows'], 20, 2);
	  add_action('manage_woocommerce_page_wc-orders_custom_column', [$this, 'mutateOrdersRows'], 20, 2);
	  

  }

  /**
   * Adds custom content to the orders table rows.
   * 
   * Adds transaction ID and network information to their respective columns
   * in the WooCommerce orders table.
   *
   * @param string $column The name of the column in the orders table.
   */
  public function mutateOrdersRows($column, $order)
  {
    if (is_int($order)){
      $order = wc_get_order($order);
    }
    if ('transactionId' === $column) {
 $transactionId = $order->get_meta('_tx_id');
      $net = $order->get_meta('_net');
      $color = $net == esc_attr("mainnet" ? "#7cc67c" : "#f1dd06");
      $link = $net == "mainnet" ? "https://explorer.xprnetwork.org/transaction/" : "https://testnet.explorer.xprnetwork.org/transaction/";
      if ($order->get_payment_method() == "xprcheckout") {
        echo '<a class="button-primary" style="color:#ffffff;background-color:' . esc_attr($color) . ';" target="_blank" href="' . esc_attr($link) . esc_attr($transactionId) . '">' . esc_attr(substr($transactionId, strlen($transactionId) - 8, strlen($transactionId))) . '</a>';
      } else {
        echo '';
      }
    }
    if ('network' === $column) {

      $net = $order->get_meta('_network');
      $color = $net == "mainnet" ? "#7cc67c" : "#f1dd06";
      if ($net == 'testnet') {
        echo '<span class="button-primary" style="color:#50575e;background-color:' . esc_attr($color) . ';" >Testnet</a>';
      } elseif ($net == 'mainnet') {
        echo '<span class="button-primary" style="color:#50575e;background-color:' . esc_attr($color) . ';" >Mainnet</a>';
      }
      echo esc_attr('');
    }
    if ('paid_token' === $column) {

      $amount = $order->get_meta('_paid_tokens');
      if (!empty($amount)) {
        echo '<span  >'.esc_attr($amount).'</a>';
      } else{
        echo '';
      }
      echo esc_attr('');
    }
  }


  /**
   * Registers WordPress filters.
   * 
   * Attaches methods to WordPress filter hooks.
   * @access private
   */
  private function registerFilters()
  {

    add_filter('manage_edit-shop_order_columns', [$this, 'mutateOrdersColumnsHeader'], 11);
	add_filter('manage_woocommerce_page_wc-orders_columns', [$this, 'mutateOrdersColumnsHeader'], 11);
	  
  }

  /**
   * Adds custom columns to the WooCommerce orders table header.
   * 
   * Adds columns for transaction IDs and network (mainnet/testnet) to the orders table.
   *
   * @param array $columns Existing columns in the orders table.
   * @return array Modified list of columns for the orders table.
   */
  public function mutateOrdersColumnsHeader($columns)
  {

    $new_columns = array();
    foreach ($columns as $column_name => $column_info) {
      $new_columns[$column_name] = $column_info;
      if ('order_status' === $column_name) {
        $new_columns['transactionId'] = __('Transaction', 'xprcheckout-webauth-gateway-for-woocommerce'); // title
        $new_columns['network'] = __('Mainnet/testnet', 'xprcheckout-webauth-gateway-for-woocommerce'); // title
        $new_columns['paid_token'] = __('Received tokens', 'xprcheckout-webauth-gateway-for-woocommerce'); // title
      }
    }
    return $new_columns;
  }
}
