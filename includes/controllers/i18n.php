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
    load_plugin_textdomain('xprcheckout', FALSE, XPRCHECKOUT_ROOT_DIR . 'i18n/languages');
  }

  /**
   * Retrieves public-facing translated strings.
   * 
   * Provides an array of translated strings that are likely to be used in public-facing interfaces 
   * related to the XPRCheckout payment gateway. Uses the plugin's text domain for translations.
   * 
   * @return array Associative array of public-facing translated strings.
   */
  public static function getPublicTranslations()
  {

    return [
      "payInviteTitle" => __('Pay with WebAuth', 'xprcheckout'),
      "payInviteText" => __('Connect your WebAuth wallet to start the payment flow.', 'xprcheckout'),
      "payInviteButtonLabel" => __('Connect WebAuth', 'xprcheckout', 'xprcheckout'),
      "orderStatusTitle" => __("Payment succesfull", 'xprcheckout'),
      "orderStatusText" => __("This order is marked as complete", 'xprcheckout'),
      "selectTokenDialogTitle" => __("Select token", 'xprcheckout'),
      "selectTokenDialogText" => __("Select the token you want to pay with.", 'xprcheckout'),
      "selectTokenDialogConnectedAs" => __("Connected as", 'xprcheckout'),
      "selectTokenDialogChangeAccountLabel" => __("change account ?", 'xprcheckout'),
      "selectTokenPayButtonLabel" => __("Pay", 'xprcheckout'),
      "selectTokenPayProcessingLabel" => __("Fetching tokens rates", 'xprcheckout'),

      "paymentProcessingLabel" => __("Waiting for transaction to complete", 'xprcheckout'),
      "paymentFailureDialogTitle" => __("Something wrong with your transfer.", 'xprcheckout'),
      "paymentFailureDialogText" => __("The transfer fail. The issue came from the chain side, but do not worry, no tokens have been transferred. Please retry or save your order to pay it later.", 'xprcheckout'),
      
      "invalidOrderDialogTitle" => __("No order found.", 'xprcheckout'),
      "invalidOrderDialogText" => __("Not order has been found at the given url. ", 'xprcheckout'),

      "verifyPaymentDialogTitle" => __("Payment verification", 'xprcheckout'),
      "verifyPaymentDialogText" => __("Please wait while we check payment information.", 'xprcheckout'),
      "verifyPaymentDialogProcessLabel" => __("Verifying payment", 'xprcheckout'),
      "verifySuccessPaymentDialogTitle" => __("Payment verified", 'xprcheckout'),
      "verifySuccessPaymentDialogText" => __("Great, your payment has be verified, order is now completed! ", 'xprcheckout'),
      "verifyFailurePaymentDialogTitle" => __("Payment verification failed", 'xprcheckout'),
      "verifyFailurePaymentDialogText" => __("Your payment could'nt been verified , order is pending! ", 'xprcheckout'),
    ];
  }
}
