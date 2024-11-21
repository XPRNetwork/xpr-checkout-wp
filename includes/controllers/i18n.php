<?php

namespace xprcheckout\i18n;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Handles the internationalization (i18n) for the XPRCheckout payment gateway plugin.
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
    load_plugin_textdomain('xprcheckout_gateway');
  }

  /**
   * Retrieves public-facing translated strings.
   * 
   * Provides an array of translated strings that are likely to be used in public-facing interfaces 
   * related to the xprcheckout_gateway payment gateway. Uses the plugin's text domain for translations.
   * 
   * @return array Associative array of public-facing translated strings.
   */
  public static function getPublicTranslations()
  {

    return [
      "payInviteTitle" => __('Pay with WebAuth', 'xprcheckout_gateway'),
      "payInviteText" => __('Connect your WebAuth wallet to start the payment flow.', 'xprcheckout_gateway'),
      "payInviteButtonLabel" => __('Connect WebAuth', 'xprcheckout_gateway'),
      "orderStatusTitle" => __("Payment succesfull", 'xprcheckout_gateway'),
      "orderStatusText" => __("This order is marked as complete", 'xprcheckout_gateway'),
      "selectTokenDialogTitle" => __("Select token", 'xprcheckout_gateway'),
      "selectTokenDialogText" => __("Select the token you want to pay with.", 'xprcheckout_gateway'),
      "selectTokenDialogConnectedAs" => __("Connected as", 'xprcheckout_gateway'),
      "selectTokenDialogChangeAccountLabel" => __("change account ?", 'xprcheckout_gateway'),
      "selectTokenPayButtonLabel" => __("Pay", 'xprcheckout_gateway'),
      "selectTokenPayProcessingLabel" => __("Fetching tokens rates", 'xprcheckout_gateway'),

      "paymentProcessingLabel" => __("Waiting for transaction to complete", 'xprcheckout_gateway'),
      "paymentFailureDialogTitle" => __("Something wrong with your transfer.", 'xprcheckout_gateway'),
      "paymentFailureDialogText" => __("The transfer fail. The issue came from the chain side, but do not worry, no tokens have been transferred. Please retry or save your order to pay it later.", 'xprcheckout_gateway'),
      
      "invalidOrderDialogTitle" => __("No order found.", 'xprcheckout_gateway'),
      "invalidOrderDialogText" => __("Not order has been found at the given url. ", 'xprcheckout_gateway'),

      "verifyPaymentDialogTitle" => __("Payment verification", 'xprcheckout_gateway'),
      "verifyPaymentDialogText" => __("Please wait while we check payment information.", 'xprcheckout_gateway'),
      "verifyPaymentDialogProcessLabel" => __("Verifying payment", 'xprcheckout_gateway'),
      "verifySuccessPaymentDialogTitle" => __("Payment verified", 'xprcheckout_gateway'),
      "verifySuccessPaymentDialogText" => __("Great, your payment has be verified, order is now completed! ", 'xprcheckout_gateway'),
      "verifyFailurePaymentDialogTitle" => __("Payment verification failed", 'xprcheckout_gateway'),
      "verifyFailurePaymentDialogText" => __("Your payment could'nt been verified , order is pending! ", 'xprcheckout_gateway'),
    ];
  }
}
