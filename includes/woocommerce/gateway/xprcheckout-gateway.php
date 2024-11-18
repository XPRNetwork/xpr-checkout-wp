<?php

use xprcheckout\config\Config;
use xprcheckout\i18n\Translations;


/**
 * Class XPRCheckoutGateway
 *
 * Extends WooCommerce Payment Gateway to provide Webauth functionality.
 */
class XPRCheckoutGateway extends WC_Payment_Gateway
{

  /**
   * XPRCheckoutGateway constructor.
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
    $this->testwallet = $this->get_option('wallet');
    $this->testnet = 'yes' === $this->get_option('testnet');
    $this->enabled = $this->get_option('enabled');
    $this->appName = $this->get_option('appName');
    $this->appLogo = $this->get_option('appLogo');
    $this->allowedTokens = $this->get_option('allowedTokens');

    //add_action( 'template_redirect', array($this,'xprcheckout_template_redirect'));
    add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
    add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
    add_action('woocommerce_email_before_order_table', array($this, 'xprcheckout_email_instructions'), 10, 3);
    add_filter( 'wc_order_statuses', array($this,'xprcheckout_add_partial_fill_order_status') );
    
    

  }

  /**
   * Setup basic properties for the gateway.
   */
  protected function setup_properties()
  {
    $this->id                 = 'xprcheckout';
    $this->icon               = apply_filters('woocommerce_cod_icon', '');
    $this->title              = __('XPR Checkout', 'xprcheckout');
    $this->method_title       = __('XPR Checkout', 'xprcheckout');
    $this->method_description = __('Provides a Webauth wallet Payment Gateway for your customer.', 'xprcheckout');
    $this->has_fields         = false;
  }

  /**
   * Initialize form fields for the gateway's settings page.
   */
  public function init_form_fields()
  {
    $this->form_fields = array(
      'enabled' => array(
        'title' => __('Enable/Disable', 'xprcheckout'),
        'type' => 'checkbox',
        'label' => __('Enable Webauth Payment', 'xprcheckout'),
        'default' => 'yes'
      ),
      'network' => array(
        'title' => __('Select network', 'xprcheckout'),
        'type' => 'select',
        'options' => [
          'mainnet' => 'Mainnet',
          'testnet' => 'Testnet',
        ],
        'label' => __('Select network', 'xprcheckout'),
        'default' => 'testnet',
        'value'
      ),
      'registered' => array(
        'title' => __('Register store ', 'xprcheckout'),
        'type' => 'xprcheckout_register',
        'description' => __('Register you store nearby the smart contract', 'xprcheckout'),
        'desc_tip'      => true,
      ),
      'title' => array(
        'title' => __('Title', 'xprcheckout'),
        'type' => 'text',
        'default' => 'Pay with WebAuth',
        'description' => __('This controls the title which the user sees during checkout.', 'xprcheckout'),
        
        'desc_tip'      => true,
      ),
      'description' => array(
        'title' => __('Description', 'xprcheckout'),
        'type' => 'textarea',
        'description' => __('This controls the title which the user sees during checkout.', 'xprcheckout'),
        'default' => __('Pay securely with with multiple crypto currencies through WebAuth with zero gas fee', 'xprcheckout'),
        'desc_tip'      => true,
      ),

      'wallet' => array(
        'title' => __('Mainnet account', 'xprcheckout'),
        'type' => 'hidden',
        'description' => __('Set the destination account on mainnet where pay token will be paid. <b>Used only when "Use testnet" option is disabled</b>', 'xprcheckout'),
        'desc_tip'      => true,
      ),
      
      'appName' => array(
        'title' => __('dApp Name', 'xprcheckout'),
        'type' => 'text',
        'description' => __('The application name displayed in the webauth modal', 'xprcheckout'),
        'default' => __('My awesome store', 'xprcheckout'),
        'desc_tip'      => true,
      ),
      /*'appLogo' => array(
          'title' => __('dApp Logo', 'xprcheckout'),
          'type' => 'text',
          'description' => __('The application logo displayed in the webauth modal', 'xprcheckout'),
          
          'desc_tip'      => true,
        ),*/
      'allowedTokens' => array(
        'title' => __('Allowed Tokens', 'xprcheckout'),
        'type' => 'text',
        'description' => __('Accepted tokens as payment for transfer, will be displayed in the payments process flow. Specify a uppercase only, coma separated, tokens list', 'xprcheckout'),
        'default' => __('XPR,XUSDC', 'xprcheckout'),
        'desc_tip'      => true,
      ),
      'polygonKey' => array(
        'title' => __('Free api key ', 'xprcheckout'),
        'type' => 'text',
        'description' => __('Your key for currency pricing service on https://app.freecurrencyapi.com/register.', 'xprcheckout'),
        
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
    

    $wallet = $this->get_option('wallet');
    $testnet = 'testnet' === $this->get_option('network');
    if (!$testnet && $wallet == "") return false;
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

    error_log('PASS THROUGHT PROCESS PAYMENT');
    $order = wc_get_order($order_id);
    

    if ($order->get_total() > 0) {
      //$order->update_status(apply_filters('woocommerce_cod_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order), __('Payment to be made upon delivery.', 'xprcheckout'));
    } else {
      //$order->payment_complete();
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
      $text = esc_html(wp_kses_post($desc));
      echo esc_attr(printf(
        '%s',
        $text // it can simply be a normal variable 
    ));
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
    wp_register_script_module('xprcheckout_public', XPRCHECKOUT_ROOT_URL . 'dist/checkout/build/app.js?v='. uniqid(), [], time());
    wp_enqueue_script_module('xprcheckout_public');
    
    wp_enqueue_style('xprcheckout_public_style', XPRCHECKOUT_ROOT_URL . 'dist/checkout/build/app.css?v='. uniqid(),[], time());
    
  }

  /**
   * Add content to the WC emails.
   *
   * @param WC_Order $order Order object.
   * @param bool     $sent_to_admin  Sent to admin.
   * @param bool     $plain_text Email format: plain text or HTML.
   */
  public function xprcheckout_email_instructions($order, $sent_to_admin, $plain_text = false)
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
  public function generate_xprcheckout_register_html($key, $data)
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

<script>
        <?php 
          
          $baseConfig = Config::GetBaseConfig();
          $baseConfig['walletInputSelector']= "#woocommerce_xprcheckout_wallet";
          
          ?>
          window.pluginConfig = <?php echo json_encode($baseConfig); ?>;
      </script>
      
    <tr valign="top">
      
      <th scope="row" class="titledesc">
        <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?>
          
        </label>
      </th>
      <td class="forminp">
        <fieldset>
          
          <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
          <div id="xpr-regstore"></div>
          <?php echo esc_attr($this->get_description_html($data)); // WPCS: XSS ok. 
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
        <label for="<?php echo esc_attr($field_key); ?>"><?php echo esc_attr($data['title']); ?> <?php echo esc_attr($this->get_tooltip_html($data)); // WPCS: XSS ok.                                                                                              
                                                                                                      ?></label>
      </th>
      <td class="forminp">
        <fieldset>
          <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
          <input class="input-text regular-input <?php echo esc_attr($data['class']); ?>" type="<?php echo esc_attr($data['type']); ?>" name="<?php echo esc_attr($field_key); ?>" id="<?php echo esc_attr($field_key); ?>" style="<?php echo esc_attr($data['css']); ?>" value="<?php echo esc_attr($this->get_option($key)); ?>" placeholder="<?php echo esc_attr($data['placeholder']); ?>" <?php disabled($data['disabled'], true); ?> <?php echo esc_attr($this->get_custom_attribute_html($data)); // WPCS: XSS ok.                                                                                                                                                                                                                                                                                                                                                                                                                                  
                                                                                                                                                                                                                                                                                                                                                                                                                                            ?> />
          <?php echo esc_attr($this->get_description_html($data)); // WPCS: XSS ok.
          ?>
        </fieldset>
      </td>
    </tr>
<?php
    return ob_get_clean();
  }
  
function xprcheckout_redirect_on_new_order($order) {
  
    error_log('xprcheckout_redirect_on_order_pay');
    error_log(print_r($posted_data,1));
    if(WC()->session->chosen_payment_method == 'xprcheckout'){
   $this->xprcheckout_redirect_to_payment_page();
   
    }
    
}

public function xprcheckout_redirect_on_order_pay($posted_data) {
  
    $paymentKey = WC()->session->get('paymentKey');
    if( is_wc_endpoint_url( 'order-received' )) {
      wp_redirect(home_url('/xprcheckout/payments/'.WC()->session->get('paymentKey')));
      exit;
    }
    
}

public function xprcheckout_add_partial_fill_order_status( $order_statuses ) {
  $new_order_statuses = $order_statuses;
  $new_order_statuses['wc-partial-fill'] = 'Partially filled payment';
  return $new_order_statuses;
}



private function xprcheckout_redirect_to_payment_page(){

    if ( ! is_ajax() ) {
			wp_safe_redirect(
				apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', home_url('/xprcheckout/payments/'.WC()->session->get('paymentKey')), $order )
			);
			exit;
		}

		wp_send_json(
			array(
				'result'   => 'success',
				'redirect' => apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', home_url('/xprcheckout/payments/'.WC()->session->get('paymentKey')), $order ),
			)
		);
    exit();
  }
}
