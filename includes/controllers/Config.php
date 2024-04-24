<?php

namespace wookey\config;
use wookey\utils\OrderResolver;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}



/**
 * Configuration handler for WooKey payment gateway.
 * 
 * This class provides methods to extract configuration details related to the WooKey payment gateway from
 * WooCommerce settings. It also allows to merge these settings with order or cart related details.
 */

class Config
{

  /**
   * Retrieves base configuration values for WooKey payment gateway.
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
  public static function GetBaseConfig($requestedPaymentKey)
  {
    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    $woocommerceCheckoutId = wc_get_page_id( 'checkout' );
    $woocommerceCheckoutUrl = get_permalink( $woocommerceCheckoutId);

    $woocommerceThankyouId = wc_get_page_id( 'order-received' );
    $woocommerceThankyouUrl = get_permalink( $woocommerceThankyouId);
    

    return array(
      "mainnetActor" => $wookeyGateway->get_option('mainwallet'),
      "testnetActor" => $wookeyGateway->get_option('testwallet'),
      "appName" => $wookeyGateway->get_option('appName'),
      "testnet" => 'testnet' === $wookeyGateway->get_option('network'),
      "network" => $wookeyGateway->get_option('network'),
      "allowedTokens" => $wookeyGateway->get_option('allowedTokens'),
      "wooCurrency" => get_woocommerce_currency(),
      "baseDomain" => get_site_url(),
      "wooCheckoutUrl" => $woocommerceCheckoutUrl,
      "wooThankYouUrl" => $woocommerceThankyouUrl,
      'nonce' => wp_create_nonce('wookey'),
      'requestedPaymentKey'=>$requestedPaymentKey
    );
  }


  /**
   * Retrieves configuration values for WooKey payment gateway merged with specific order details.
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
    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    $resolved = OrderResolver::Process($requestedPaymentKey,$wookeyGateway->get_option('network'));
    return array_merge($baseConfig, ['order'=>$resolved]);

  }

  /**
   * Retrieves configuration values for WooKey payment gateway merged with cart details.
   * 
   * @return array Associative array containing base configuration from self::GetBaseConfig() merged with:
   *      - "cartTotal"     => float, The total amount in the cart.
   *      - "paymentKey"    => string, The payment key associated with the current cart.
   * @access public
   * @static
   */
  
}
