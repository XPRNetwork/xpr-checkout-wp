<?php

namespace xprcheckout\cart;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


/**
 * Represents the XPRCheckout cart functionality.
 *
 * This class is responsible for handling cart-related tasks such as generating a unique payment key as SHA256 for the cart to bind with the smart contract.
 */

class Cart
{

  /**
   * Cart constructor.
   * 
   * Initializes the class and registers the necessary WooCommerce actions.
   */
  public function __construct()
  {
    $this->registerActions();
  }

  /**
   * Registers WooCommerce actions.
   * 
   * This method attaches class methods to WooCommerce action hooks.
   */
  public function registerActions()
  {

    add_action('woocommerce_cart_totals_before_order_total', [$this, 'generatePaymentKey'], 99, 2);
  }

  /**
   * Generates a unique payment key for the current cart.
   * 
   * This method creates a hash based on the current cart contents and the current time, and saves this hash
   * as a "paymentKey" in the WooCommerce session. It is intended to be used as a unique identifier for the cart.
   *
   * @param array $cart_item_data The current cart item data.
   */
  public function generatePaymentKey($cart_item_data)
  {

    $cart = WC()->cart->get_cart();
    $serializedCart = wp_json_encode($cart);
    $newHash = $cart ? hash('sha256', $serializedCart . time()) : '';
    WC()->session->set('paymentKey', $newHash);
  }
}
