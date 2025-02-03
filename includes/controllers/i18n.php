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
    load_plugin_textdomain('xprcheckout-webauth-gateway-for-e-commerce');
  }

  /**
   * Retrieves public-facing translated strings.
   * 
   * Provides an array of translated strings that are likely to be used in public-facing interfaces 
   * related to the xprcheckout-webauth-gateway-for-e-commerce payment gateway. Uses the plugin's text domain for translations.
   * 
   * @return array Associative array of public-facing translated strings.
   */
  public static function getPublicTranslations()
  {

    return [
      "payInviteTitle" => __('Pay with WebAuth', 'xprcheckout-webauth-gateway-for-e-commerce'),
      "payInviteText" => __('Connect your WebAuth wallet to start the payment flow.', 'xprcheckout-webauth-gateway-for-e-commerce'),
      "payInviteButtonLabel" => __('Connect WebAuth', 'xprcheckout-webauth-gateway-for-e-commerce'),
      "orderStatusTitle" => __("Payment succesfull", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "orderStatusText" => __("This order is marked as complete", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenDialogTitle" => __("Select token", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenDialogText" => __("Select the token you want to pay with.", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenDialogConnectedAs" => __("Connected as", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenDialogChangeAccountLabel" => __("change account ?", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenPayButtonLabel" => __("Pay", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "selectTokenPayProcessingLabel" => __("Fetching tokens rates", 'xprcheckout-webauth-gateway-for-e-commerce'),

      "paymentProcessingLabel" => __("Waiting for transaction to complete", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "paymentFailureDialogTitle" => __("Something wrong with your transfer.", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "paymentFailureDialogText" => __("The transfer fail. The issue came from the chain side, but do not worry, no tokens have been transferred. Please retry or save your order to pay it later.", 'xprcheckout-webauth-gateway-for-e-commerce'),
      
      "invalidOrderDialogTitle" => __("No order found.", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "invalidOrderDialogText" => __("Not order has been found at the given url. ", 'xprcheckout-webauth-gateway-for-e-commerce'),

      "verifyPaymentDialogTitle" => __("Payment verification", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifyPaymentDialogText" => __("Please wait while we check payment information.", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifyPaymentDialogProcessLabel" => __("Verifying payment", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifySuccessPaymentDialogTitle" => __("Payment verified", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifySuccessPaymentDialogText" => __("Great, your payment has be verified, order is now completed! ", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifyFailurePaymentDialogTitle" => __("Payment verification failed", 'xprcheckout-webauth-gateway-for-e-commerce'),
      "verifyFailurePaymentDialogText" => __("Your payment could'nt been verified , order is pending! ", 'xprcheckout-webauth-gateway-for-e-commerce'),
    ];
  }
}
