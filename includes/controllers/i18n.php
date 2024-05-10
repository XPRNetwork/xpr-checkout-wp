<?php

namespace wookey\i18n;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Handles the internationalization (i18n) for the Wookey payment gateway plugin.
 * 
 * This class provides a mechanism for registering and loading the plugin's text domain 
 * as well as retrieving translated strings intended for public-facing interfaces.
 * 
 */
class Translations
{

  /**
   * Translations constructor.
   * 
   * Initializes the translations handler and registers necessary actions.
   */
  public function __construct()
  {

    $this->registerActions();
  }

  /**
   * Registers WordPress actions.
   * 
   * Attaches methods to WordPress action hooks.
   * @access private
   */
  private function registerActions()
  {
    add_action('plugins_loaded', [$this, 'initTextDomain']);
  }

  /**
   * Initializes the plugin's text domain for translations.
   * 
   * Loads the MO file for the text domain based on the site's locale.
   */
  function initTextDomain()
  {
    load_plugin_textdomain('wookey', FALSE, WOOKEY_ROOT_DIR . 'i18n/languages');
  }

  /**
   * Retrieves public-facing translated strings.
   * 
   * Provides an array of translated strings that are likely to be used in public-facing interfaces 
   * related to the Wookey payment gateway. Uses the plugin's text domain for translations.
   * 
   * @return array Associative array of public-facing translated strings.
   */
  public static function getPublicTranslations()
  {

    return [
      "payInviteTitle" => __('Pay with WebAuth', 'wookey'),
      "payInviteText" => __('Connect your WebAuth wallet to start the payment flow.', 'wookey'),
      "payInviteButtonLabel" => __('Connect WebAuth', 'wookey', 'wookey'),
      "orderStatusTitle" => __("Payment succesfull", 'wookey'),
      "orderStatusText" => __("This order is marked as complete", 'wookey'),
      "selectTokenDialogTitle" => __("Select token", 'wookey'),
      "selectTokenDialogText" => __("Select the token you want to pay with.", 'wookey'),
      "selectTokenDialogConnectedAs" => __("Connected as", 'wookey'),
      "selectTokenDialogChangeAccountLabel" => __("change account ?", 'wookey'),
      "selectTokenPayButtonLabel" => __("Pay", 'wookey'),
      "selectTokenPayProcessingLabel" => __("Fetching tokens rates", 'wookey'),

      "paymentProcessingLabel" => __("Waiting for transaction to complete", 'wookey'),
      "paymentFailureDialogTitle" => __("Something wrong with your transfer.", 'wookey'),
      "paymentFailureDialogText" => __("The transfer fail. The issue came from the chain side, but do not worry, no tokens have been transferred. Please retry or save your order to pay it later.", 'wookey'),
      
      "invalidOrderDialogTitle" => __("No order found.", 'wookey'),
      "invalidOrderDialogText" => __("Not order has been found at the given url. ", 'wookey'),

      "verifyPaymentDialogTitle" => __("Payment verification", 'wookey'),
      "verifyPaymentDialogText" => __("Please wait while we check payment information.", 'wookey'),
      "verifyPaymentDialogProcessLabel" => __("Verifying payment", 'wookey'),
      "verifySuccessPaymentDialogTitle" => __("Payment verified", 'wookey'),
      "verifySuccessPaymentDialogText" => __("Great, your payment has be verified, order is now completed! ", 'wookey'),
      "verifyFailurePaymentDialogTitle" => __("Payment verification failed", 'wookey'),
      "verifyFailurePaymentDialogText" => __("Your payment could'nt been verified , order is pending! ", 'wookey'),
    ];
  }
}
