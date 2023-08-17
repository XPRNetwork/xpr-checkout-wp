<?php

namespace wookey\config;


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
  public static function GetBaseConfig()
  {
    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    $mainnetActor = $wookeyGateway->get_option('mainwallet');
    $testnetActor = $wookeyGateway->get_option('testwallet');
    return array(
      "mainnetActor" => $mainnetActor,
      "testnetActor" => $testnetActor,
      "testnet" => 'testnet' === $wookeyGateway->get_option('network'),
      "network" => $wookeyGateway->get_option('network'),
      "allowedTokens" => $wookeyGateway->get_option('allowedTokens'),
      "wooCurrency" => get_woocommerce_currency(),
      "baseDomain" => get_site_url()
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
  public static function GetConfigWithOrder($orderId)
  {
    $order = wc_get_order($orderId);
    $baseConfig = self::GetBaseConfig();
    $extendedConfig = [];
    if (!$order) {
      return array_merge($baseConfig, $extendedConfig);
    }
    $extendedConfig["transactionId"] = $order->get_meta('_transactionId');
    $extendedConfig["network"] = $order->get_meta('_net');
    $extendedConfig["paymentKey"] = $order->get_meta('_paymentKey');
    $extendedConfig["orderTotal"] = $order->get_total();
    return array_merge($baseConfig, $extendedConfig);
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
  public static function GetConfigWithCart()
  {
    $baseConfig = self::GetBaseConfig();
    $extendedConfig = [];
    if (!isset(WC()->cart)) {
      return array_merge($baseConfig, $extendedConfig);
    }
    $extendedConfig["cartTotal"] = WC()->cart->total;
    $extendedConfig["paymentKey"] = WC()->session->get('paymentKey');

    return array_merge($baseConfig, $extendedConfig);
  }
}
