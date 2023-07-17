<?php
add_filter('woocommerce_payment_gateways', 'proton_add_gateway_class');
function proton_add_gateway_class($gateways)
{
  error_log('it bootstrap');
  $gateways[] = 'WC_proton_Gateway';
  return $gateways;
}

add_action('admin_enqueue_scripts', 'proton_add_admin_script');
function proton_add_admin_script()
{
  error_log('it add admin script');
  wp_register_script('proton-wc-gateway-admin', PWG_ROOT_URL . 'dist/admin/proton-wc-admin.iife.js?v=' . uniqid(), [], time(), true);
  wp_enqueue_script('proton-wc-gateway-admin');
}

add_action('plugins_loaded', 'proton_init_gateway_class');
function proton_init_gateway_class()
{

  class WC_proton_Gateway extends WC_Payment_Gateway
  {
    public function __construct()
    {

      $this->id = 'wagateway';
      $this->icon = '';
      $this->has_fields = true;
      $this->method_title = 'Webauth payment gateway';
      $this->method_description = 'Allow user to use tokens from his WebAuth wallet as payment method';
      $this->supports = array(
        'products'
      );

      $this->title = $this->get_option('title');
      $this->description = $this->get_option('description');
      $this->mainwallet = $this->get_option('mainwallet');
      $this->testwallet = $this->get_option('testwallet');
      $this->testnet = 'yes' === $this->get_option('testnet');
      $this->enabled = $this->get_option('enabled');
      $this->appName = $this->get_option('appName');
      $this->appLogo = $this->get_option('appLogo');
      $this->allowedTokens = $this->get_option('allowedTokens');

      //add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
      //add_action('woocommerce_checkout_create_order', array($this, 'before_checkout_create_order'), 20, 2);
      //  add_action('woocommerce_after_checkout_form', array($this, 'move_payment_form_after_order_creation'), 5);


      $this->init_form_fields();
      $this->init_settings();

      error_log('it construct');
    }

    public function init_form_fields()
    {

      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', 'woocommerce'),
          'type' => 'checkbox',
          'label' => __('Enable Webauth Payment', 'woocommerce'),
          'default' => 'yes'
        ),
        'testnet' => array(
          'title' => __('Use testnet', 'woocommerce'),
          'type' => 'checkbox',
          'label' => __('Enable testnet', 'woocommerce'),
          'default' => 'yes'
        ),
        'title' => array(
          'title' => __('Title', 'woocommerce'),
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
          'default' => __('WebAuth payment', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'mainwallet' => array(
          'title' => __('Mainnet account', 'woocommerce'),
          'type' => 'text',
          'description' => __('Set the destination account on mainnet where pay token will be paid. <b>Used only when "Use testnet" option is disabled</b>', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'testwallet' => array(
          'title' => __('Testnet account', 'woocommerce'),
          'type' => 'text',
          'description' => __('Set the destination account on testnet where pay token will be paid. Used only when "Use testnet" option is enabled.', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'appName' => array(
          'title' => __('dApp Name', 'woocommerce'),
          'type' => 'text',
          'description' => __('The application name displayed in the webauth modal', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'appLogo' => array(
          'title' => __('dApp Logo', 'woocommerce'),
          'type' => 'text',
          'description' => __('The application logo displayed in the webauth modal', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'allowedTokens' => array(
          'title' => __('Allowed Tokens', 'woocommerce'),
          'type' => 'text',
          'description' => __('Accepted tokens as payment for transfer, will be displayed in the payments process flow. Specify a uppercase only, coma separated, tokens list', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'polygonKey' => array(
          'title' => __('Polygon API key ', 'woocommerce'),
          'type' => 'text',
          'description' => __('Your key for currency pricing service on polygon.io.', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        ),
        'registered' => array(
          'title' => __('Register store ', 'woocommerce'),
          'type' => 'cool',
          'description' => __('Register you store nearby the smart contract', 'woocommerce'),
          'default' => __('', 'woocommerce'),
          'desc_tip'      => true,
        )

      );
    }

    public function payment_fields()
    {
    }

    public function validate_fields()
    {
      return true;
    }

    public function payment_scripts()
    {
    }
  }
}
