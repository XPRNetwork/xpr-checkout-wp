<?php

namespace xprcheckout\config;
use xprcheckout\utils\OrderResolver;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}



/**
 * Configuration handler for XPRCheckout payment gateway.
 * 
 * This class provides methods to extract configuration details related to the XPRCheckout payment gateway from
 * WooCommerce settings. It also allows to merge these settings with order or cart related details.
 */

class Config
{

  /**
   * Retrieves base configuration values for XPRCheckout payment gateway.
   *
   * @return array Associative array containing:
   *      - "mainnetActor"   => string, The main wallet address for transactions.
   *      - "testnetActor"   => string, The test wallet address for transactions.
   *      - "testnet"       => bool, Whether the testnet mode is active or not.
   *      - "network"       => string, The network option chosen.
   *      - "allowedTokens" => mixed, The allowed tokens for transactions.
   *      - "wooCurrency"   => string, The active currency in WooCommerce.
   *      - "baseDomain"    => string, The base URL of the website.
   * @access public
   * @static
   */
  public static function GetDashbordConfig()
  {
    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    return array(
      "mainnetActor" => $xprcheckoutGateway->get_option('mainwallet'),
      "testnetActor" => $xprcheckoutGateway->get_option('testwallet'),
      "appName" => $xprcheckoutGateway->get_option('appName'),
      "testnet" => 'testnet' === $xprcheckoutGateway->get_option('network'),
      "network" => $xprcheckoutGateway->get_option('network'),
      "allowedTokens" => $xprcheckoutGateway->get_option('allowedTokens'),
      "baseDomain" => get_site_url(),
      "wooCurrency" => get_woocommerce_currency(),
    );
  }
  public static function GetBaseConfig($requestedPaymentKey)
  {
    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    $wcCheckoutId = wc_get_page_id( 'checkout' );
    $wcCheckoutUrl = get_permalink( $wcCheckoutId);

    $wcThankyouId = wc_get_page_id( 'order-received' );
    $wcThankyouUrl = get_permalink( $wcThankyouId);
    

    return array(
      "mainnetActor" => $xprcheckoutGateway->get_option('mainwallet'),
      "testnetActor" => $xprcheckoutGateway->get_option('testwallet'),
      "appName" => $xprcheckoutGateway->get_option('appName'),
      "testnet" => 'testnet' === $xprcheckoutGateway->get_option('network'),
      "network" => $xprcheckoutGateway->get_option('network'),
      "allowedTokens" => $xprcheckoutGateway->get_option('allowedTokens'),
      "wooCurrency" => get_woocommerce_currency(),
      "baseDomain" => get_site_url(),
      "wooCheckoutUrl" => $wcCheckoutUrl,
      "wooThankYouUrl" => $wcThankyouUrl,
      'nonce' => wp_create_nonce('xprcheckout'),
      'requestedPaymentKey'=>$requestedPaymentKey
    );
  }


  /**
   * Retrieves configuration values for XPRCheckout payment gateway merged with specific order details.
   * 
   * @param int $orderId The WooCommerce order ID.
   * @return array Associative array containing base configuration from self::GetBaseConfig() merged with:
   *      - "transactionId" => string, The transaction ID associated with the order.
   *      - "network"       => string, The network used for the order transaction.
   *      - "paymentKey"    => string, The payment key associated with the order.
   *      - "orderTotal"    => float, The total amount of the order.
   * @access public
   * @static
   */
  public static function GetConfigWithOrder($requestedPaymentKey)
  {

    $baseConfig = self::GetBaseConfig($requestedPaymentKey);
    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    $resolved = OrderResolver::Process($requestedPaymentKey,$xprcheckoutGateway->get_option('network'));
    return array_merge($baseConfig, ['order'=>$resolved]);

  }
  
  public static function GetConfigWithOrderById($orderId)
  {

    $order = new \WC_Order( $orderId );
    $requestedPaymentKey = $order->get_meta('_payment_key');
    $baseConfig = self::GetBaseConfig($requestedPaymentKey);
    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    $resolved = OrderResolver::Process($requestedPaymentKey,$xprcheckoutGateway->get_option('network'));
    return array_merge($baseConfig, ['order'=>$resolved]);

  }

  /**
   * Retrieves configuration values for XPRCheckout payment gateway merged with cart details.
   * 
   * @return array Associative array containing base configuration from self::GetBaseConfig() merged with:
   *      - "cartTotal"     => float, The total amount in the cart.
   *      - "paymentKey"    => string, The payment key associated with the current cart.
   * @access public
   * @static
   */
  
}
