<?php

use wookey\config\Config;
use wookey\i18n\Translations;

/**
 * Class WC_WookeyGateway
 *
 * Extends WooCommerce Payment Gateway to provide Webauth functionality.
 */
class WC_WookeyGateway extends WC_Payment_Gateway
{

  /**
   * WC_WookeyGateway constructor.
   *
   * Set up the gateway's properties and initializes settings.
   */
  public function __construct()
  {

    $this->setup_properties();
    $this->init_form_fields();
    $this->init_settings();

    $this->title = $this->get_option('title');
    $this->description = $this->get_option('description');
    $this->mainwallet = $this->get_option('mainwallet');
    $this->testwallet = $this->get_option('testwallet');
    $this->testnet = 'yes' === $this->get_option('testnet');
    $this->enabled = $this->get_option('enabled');
    $this->appName = $this->get_option('appName');
    $this->appLogo = $this->get_option('appLogo');
    $this->allowedTokens = $this->get_option('allowedTokens');

    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
    add_action('woocommerce_email_before_order_table', array($this, 'wookey_email_instructions'), 10, 3);
  }

  /**
   * Setup basic properties for the gateway.
   */
  protected function setup_properties()
  {
    $this->id                 = 'wookey';
    $this->icon               = apply_filters('woocommerce_cod_icon', '');
    $this->method_title       = __('Webauth for woocommerce', 'wookey');
    $this->method_description = __('Provides a Webauth Payment Gateway for your customer.', 'wookey');
    $this->has_fields         = false;
  }

  /**
   * Initialize form fields for the gateway's settings page.
   */
  public function init_form_fields()
  {
    $this->form_fields = array(
      'enabled' => array(
        'title' => __('Enable/Disable', 'wookey'),
        'type' => 'checkbox',
        'label' => __('Enable Webauth Payment', 'wookey'),
        'default' => 'yes'
      ),
      'testnet' => array(
        'title' => __('Use testnet', 'wookey'),
        'type' => 'checkbox',
        'label' => __('Enable testnet', 'wookey'),
        'default' => 'yes'
      ),
      'network' => array(
        'title' => __('Select network', 'wookey'),
        'type' => 'select',
        'options' => [
          'mainnet' => 'Mainnet',
          'testnet' => 'Testnet',
        ],
        'label' => __('Select network', 'wookey'),
        'default' => 'yes',
        'value'
      ),
      'registered' => array(
        'title' => __('Register store ', 'wookey'),
        'type' => 'wookey_register',
        'description' => __('Register you store nearby the smart contract', 'wookey'),
        'default' => __('', 'wookey'),
        'desc_tip'      => true,
      ),
      'title' => array(
        'title' => __('Title', 'wookey'),
        'type' => 'text',
        'description' => __('This controls the title which the user sees during checkout.', 'wookey'),
        'default' => __('WebAuth payment', 'wookey'),
        'desc_tip'      => true,
      ),
      'description' => array(
        'title' => __('Description', 'wookey'),
        'type' => 'textarea',
        'description' => __('This controls the title which the user sees during checkout.', 'wookey'),
        'default' => __('Pay securely with with multiple crypto currencies through WebAuth with zero gas fee', 'wookey'),
        'desc_tip'      => true,
      ),

      'mainwallet' => array(
        'title' => __('Mainnet account', 'wookey'),
        'type' => 'hidden',
        'description' => __('Set the destination account on mainnet where pay token will be paid. <b>Used only when "Use testnet" option is disabled</b>', 'wookey'),
        'default' => __('', 'wookey'),
        'desc_tip'      => true,
      ),
      'testwallet' => array(
        'title' => __('Testnet account', 'wookey'),
        'type' => 'hidden',
        'description' => __('Set the destination account on testnet where pay token will be paid. Used only when "Use testnet" option is enabled.', 'wookey'),
        'default' => __('', 'wookey'),
        'desc_tip'      => true,
      ),
      'appName' => array(
        'title' => __('dApp Name', 'wookey'),
        'type' => 'text',
        'description' => __('The application name displayed in the webauth modal', 'wookey'),
        'default' => __('My awesome store', 'wookey'),
        'desc_tip'      => true,
      ),
      /*'appLogo' => array(
          'title' => __('dApp Logo', 'wookey'),
          'type' => 'text',
          'description' => __('The application logo displayed in the webauth modal', 'wookey'),
          'default' => __('', 'wookey'),
          'desc_tip'      => true,
        ),*/
      'allowedTokens' => array(
        'title' => __('Allowed Tokens', 'wookey'),
        'type' => 'text',
        'description' => __('Accepted tokens as payment for transfer, will be displayed in the payments process flow. Specify a uppercase only, coma separated, tokens list', 'wookey'),
        'default' => __('XPR,XUSDC', 'wookey'),
        'desc_tip'      => true,
      ),
      'polygonKey' => array(
        'title' => __('Free api key ', 'wookey'),
        'type' => 'text',
        'description' => __('Your key for currency pricing service on freeapi.io.', 'wookey'),
        'default' => __('', 'wookey'),
        'desc_tip'      => true,
      ),
    );
  }

  /**
   * Determine if the payment method is available.
   *
   * @return bool
   */
  public function is_available()
  {
    $mainwallet = $this->get_option('mainwallet');
    $testwallet = $this->get_option('testwallet');
    $testnet = 'testnet' === $this->get_option('network');
    if ($testnet && $testwallet == "") return false;
    if (!$testnet && $mainwallet == "") return false;
    return parent::is_available();
  }

  /**
   * Determine if the testnet is used.
   *
   * @return bool
   */
  public function is_testnet()
  {
    return  'testnet' === $this->get_option('network');
  }

  /**
   * Process the payment.
   *
   * @param int $order_id The ID of the order.
   *
   * @return array
   */
  public function process_payment($order_id)
  {

    $order = wc_get_order($order_id);
    $order->update_meta_data('_paymentKey', WC()->session->get('paymentKey'));
    $order->update_meta_data('_transactionId', WC()->session->get('transactionId'));

    if ($order->get_total() > 0) {
      $order->update_status(apply_filters('woocommerce_cod_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order), __('Payment to be made upon delivery.', 'wookey'));
    } else {
      $order->payment_complete();
    }
    WC()->cart->empty_cart();
    return array(
      'result'   => 'success',
      'redirect' => $this->get_return_url($order),
    );
  }

  /**
   * Display payment fields on the checkout page.
   */
  public function payment_fields()
  {

    if (!$this->is_available()) return;
    if ($this->description) {
      $desc = $this->description;
      if ($this->testnet) {
        $desc = ' <b>TESTNET ENABLED.</b><br>';
        $desc .= $this->description;
        $desc  = trim($desc);
      }
      echo wpautop('<div id="wookey-checkout"></div>');
      echo wpautop(wp_kses_post($desc));
    }
  }

  /**
   * Enqueue necessary scripts and styles.
   */
  public function payment_scripts()
  {
    global $woocommerce;
    $cart = $woocommerce->cart;
    if ('no' === $this->enabled) {
      return;
    };

    if (!$this->is_available()) return;

    $paymentKey = WC()->session->get('paymentKey');
    $cart = WC()->cart;
    $baseConfig = Config::GetConfigWithCart();
    $translations = ["translations" => Translations::getPublicTranslations()];

    wp_register_script('wookey_public', WOOKEY_ROOT_URL . 'dist/public/checkout/wookey.public.iife.js?v=' . uniqid(), [], time(), true);
    wp_localize_script('wookey_public', 'params', array_merge($baseConfig, $translations));
    wp_enqueue_script('wookey_public');
    wp_enqueue_style('wookey_public_style', WOOKEY_ROOT_URL . 'dist/public/checkout/wookey.public.css?v=' . uniqid());
    wp_enqueue_style('wookey_layout_style', WOOKEY_ROOT_URL . 'dist/public/public.css?v=' . uniqid());
  }

  /**
   * Add content to the WC emails.
   *
   * @param WC_Order $order Order object.
   * @param bool     $sent_to_admin  Sent to admin.
   * @param bool     $plain_text Email format: plain text or HTML.
   */
  public function wookey_email_instructions($order, $sent_to_admin, $plain_text = false)
  {
    if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method()) {
      echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
    }
  }

  /**
   * Generate custom HTML for the store registration field.
   *
   * @param string $key  The field key.
   * @param array  $data Associated data for the field.
   *
   * @return string
   */
  public function generate_wookey_register_html($key, $data)
  {
    $field_key = $this->get_field_key($key);
    $defaults  = array(
      'title'             => '',
      'disabled'          => false,
      'class'             => '',
      'css'               => '',
      'placeholder'       => '',
      'type'              => 'text',
      'desc_tip'          => false,
      'description'       => '',
      'custom_attributes' => array(),
    );

    $data = wp_parse_args($data, $defaults);
    ob_start();
?>

    <tr valign="top">
      <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?>
          <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok. 
          ?>
        </label>
      </th>
      <td class="forminp">
        <fieldset>
          <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
          <div id="wookey-regstore"></div>
          <?php echo $this->get_description_html($data); // WPCS: XSS ok. 
          ?>
        </fieldset>
      </td>
    </tr>
  <?php
    return ob_get_clean();
  }

  /**
   * Generate HTML for hidden fields.
   *
   * @param string $key  The field key.
   * @param array  $data Associated data for the field.
   *
   * @return string
   */
  public function generate_hidden_html($key, $data)
  {
    $field_key = $this->get_field_key($key);
    $defaults  = array(
      'title'             => '',
      'disabled'          => false,
      'class'             => '',
      'css'               => '',
      'placeholder'       => '',
      'type'              => 'text',
      'desc_tip'          => false,
      'description'       => '',
      'custom_attributes' => array(),
    );

    $data = wp_parse_args($data, $defaults);
    ob_start();
  ?>
    <tr valign="top" class="hidden">
      <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.                                                                                              
                                                                                                      ?></label>
      </th>
      <td class="forminp">
        <fieldset>
          <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
          <input class="input-text regular-input <?php echo esc_attr($data['class']); ?>" type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($field_key); ?>" style="<?php echo esc_attr($data['css']); ?>" value="<?php echo esc_attr($this->get_option($key)); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo $this->get_custom_attribute_html($data); // WPCS: XSS ok.                                                                                                                                                                                                                                                                                                                                                                                                                                  
                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> />
          <?php echo $this->get_description_html($data); // WPCS: XSS ok.
          ?>
        </fieldset>
      </td>
    </tr>
<?php
    return ob_get_clean();
  }
}
