<?php

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Cash on Delivery Gateway.
 *
 * Provides a Webauth Payment Gateway for your customer.
 *
 * @class       WC_Woow_Gateway
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     WooCommerce\Classes\Payment
 */

add_action('admin_enqueue_scripts', 'woow_add_styles');
function woow_add_styles()
{
  wp_enqueue_style('woow_dashboard', PWG_ROOT_URL . 'dist/admin/layout/dashboard.css?v=' . uniqid());
}

add_action('admin_menu', 'woow_register_woow_dashboard', 99);
function woow_register_woow_dashboard()
{
  add_submenu_page('woocommerce', 'Woow dashboard', 'Woow dashboard', 'manage_options', 'my-custom-submenu-page', 'render_woow_dashboard');
}
function render_woow_dashboard()
{
  echo '<h3>Woow Dashboard</h3>';
  include_once PWG_ROOT_DIR . 'includes/woocommerce/layouts/woow-dashboard.php';
  $dashboardLayout = new WoowDashboadLayout();
  echo $dashboardLayout->render();
}

add_filter('woocommerce_payment_gateways', 'woow_add_gateway_class');
function woow_add_gateway_class($gateways)
{

  $gateways[] = 'WC_Woow_Gateway';
  return $gateways;
}
add_filter('manage_edit-shop_order_columns', 'woow_custom_orders_list_columns', 11);
function woow_custom_orders_list_columns($columns)
{

  $new_columns = array();
  foreach ($columns as $column_name => $column_info) {
    $new_columns[$column_name] = $column_info;
    if ('order_status' === $column_name) {
      $new_columns['txid'] = __('Transaction', 'woow'); // title
      $new_columns['net'] = __('Mainnet/testnet', 'woow'); // title
    }
  }
  return $new_columns;
}

add_action('manage_shop_order_posts_custom_column', 'woow_custom_orders_list_column_content', 20, 2);
function woow_custom_orders_list_column_content($column)
{

  global $post;
  if ('txid' === $column) {
    $order = wc_get_order($post->ID);
    $txId = $order->get_meta('_txId');
    $net = $order->get_meta('_net');
    $color = $net == "mainnet" ? "#7cc67c" : "#f1dd06";
    $link = $net == "mainnet" ? "https://protonscan.io/transaction/" : "https://testnet.protonscan.io/transaction/";
    if ($order->get_payment_method()) {
      echo '<a class="button-primary" style="color:#50575e;background-color:' . $color . ';" target="_blank" href="' . $link . $txId . '">' . substr($txId, strlen($txId) - 8, strlen($txId)) . '</a>';
    } else {
      echo '';
    }
  }
  if ('net' === $column) {
    $order = wc_get_order($post->ID);
    $net = $order->get_meta('_net');
    $color = $net == "mainnet" ? "#7cc67c" : "#f1dd06";
    if ($net == 'testnet') {
      echo '<span class="button-primary" style="color:#50575e;background-color:' . $color . ';" >Testnet</a>';
    } elseif ($net == 'mainnet') {
      echo '<span class="button-primary" style="color:#50575e;background-color:' . $color . ';" >Mainnet</a>';
    }
    echo '';
  }
}

add_action('plugins_loaded', 'woow_init_gateway_class');
function woow_init_gateway_class()
{
  class WC_Woow_Gateway extends WC_Payment_Gateway
  {

    /**
     * Constructor for the gateway.
     */
    public function __construct()
    {
      // Setup general properties.
      $this->setup_properties();

      // Load the settings.
      $this->init_form_fields();
      $this->init_settings();

      // Get settings.
      $this->title = $this->get_option('title');
      $this->description = $this->get_option('description');
      $this->mainwallet = $this->get_option('mainwallet');
      $this->testwallet = $this->get_option('testwallet');
      $this->testnet = 'yes' === $this->get_option('testnet');
      $this->enabled = $this->get_option('enabled');
      $this->appName = $this->get_option('appName');
      $this->appLogo = $this->get_option('appLogo');
      $this->allowedTokens = $this->get_option('allowedTokens');

      // Actions.
      add_action('woocommerce_update_options_payment_gateways_' . $this->id, array($this, 'process_admin_options'));
      add_action('woocommerce_thankyou_' . $this->id, array($this, 'woow_thankyou_page'));
      add_filter('woocommerce_payment_complete_order_status', array($this, 'woow_change_payment_complete_order_status'), 10, 3);
      add_action('woocommerce_checkout_create_order', array($this, 'woow_custom_meta_to_order'), 20, 1);
      add_filter('woocommerce_locate_template', array($this, 'woow_relocate_plugin_template'), 1, 3);
      add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
      add_action('admin_enqueue_scripts', array($this, 'woow_add_admin_script'));
      add_action('manage_shop_order_posts_custom_column', array($this, 'woow_custom_orders_list_column_content'), 20, 2);
      // Customer Emails.
      add_action('woocommerce_email_before_order_table', array($this, 'woow_email_instructions'), 10, 3);
    }

    /**
     * Setup general properties for the gateway.
     */
    protected function setup_properties()
    {
      $this->id                 = 'woow';
      $this->icon               = apply_filters('woocommerce_cod_icon', '');
      $this->method_title       = __('Webauth for woocommerce', 'woow');
      $this->method_description = __('Provides a Webauth Payment Gateway for your customer.', 'woow');
      $this->has_fields         = false;
    }

    /**
     * Initialise Gateway Settings Form Fields.
     */
    public function init_form_fields()
    {
      $this->form_fields = array(
        'enabled' => array(
          'title' => __('Enable/Disable', 'woow'),
          'type' => 'checkbox',
          'label' => __('Enable Webauth Payment', 'woow'),
          'default' => 'yes'
        ),
        'testnet' => array(
          'title' => __('Use testnet', 'woow'),
          'type' => 'checkbox',
          'label' => __('Enable testnet', 'woow'),
          'default' => 'yes'
        ),
        'title' => array(
          'title' => __('Title', 'woow'),
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'woow'),
          'default' => __('WebAuth payment', 'woow'),
          'desc_tip'      => true,
        ),
        'description' => array(
          'title' => __('Description', 'woow'),
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'woow'),
          'default' => __('pay securly with with multiple crypto currencies through Webauth with NO GAS FEE BABY !', 'woow'),
          'desc_tip'      => true,
        ),
        'registered' => array(
          'title' => __('Register store ', 'woow'),
          'type' => 'woow_register',
          'description' => __('Register you store nearby the smart contract', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'mainwallet' => array(
          'title' => __('Mainnet account', 'woow'),
          'type' => 'hidden',
          'description' => __('Set the destination account on mainnet where pay token will be paid. <b>Used only when "Use testnet" option is disabled</b>', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'testwallet' => array(
          'title' => __('Testnet account', 'woow'),
          'type' => 'hidden',
          'description' => __('Set the destination account on testnet where pay token will be paid. Used only when "Use testnet" option is enabled.', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'appName' => array(
          'title' => __('dApp Name', 'woow'),
          'type' => 'text',
          'description' => __('The application name displayed in the webauth modal', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'appLogo' => array(
          'title' => __('dApp Logo', 'woow'),
          'type' => 'text',
          'description' => __('The application logo displayed in the webauth modal', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'allowedTokens' => array(
          'title' => __('Allowed Tokens', 'woow'),
          'type' => 'text',
          'description' => __('Accepted tokens as payment for transfer, will be displayed in the payments process flow. Specify a uppercase only, coma separated, tokens list', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),
        'polygonKey' => array(
          'title' => __('Polygon API key ', 'woow'),
          'type' => 'text',
          'description' => __('Your key for currency pricing service on polygon.io.', 'woow'),
          'default' => __('', 'woow'),
          'desc_tip'      => true,
        ),


      );
    }

    /**
     * Check If The Gateway Is Available For Use.
     *
     * @return bool
     */
    public function is_available()
    {
      return true;

      return parent::is_available();
    }

    /**
     * Process the payment and return the result.
     *
     * @param int $order_id Order ID.
     * @return array
     */
    public function process_payment($order_id)
    {

      $order = wc_get_order($order_id);

      if ($order->get_total() > 0) {
        // Mark as processing or on-hold (payment won't be taken until delivery).
        $order->update_status(apply_filters('woocommerce_cod_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order), __('Payment to be made upon delivery.', 'woow'));
      } else {
        $order->payment_complete();
      }

      // Remove cart.
      WC()->cart->empty_cart();

      // Return thankyou redirect.
      return array(
        'result'   => 'success',
        'redirect' => $this->get_return_url($order),
      );
    }

    /** 
     * Render the payment field
     */
    public function payment_fields()
    {

      if ($this->description) {
        // you can instructions for test mode, I mean test card numbers etc.
        $desc = $this->description;
        if ($this->testnet) {
          $desc = ' <b>TESTNET ENABLED.</b><br>';
          $desc .= $this->description;
          $desc  = trim($desc);
        }
        // display the description with <p> tags etc.
        echo wpautop(wp_kses_post($desc));
      }
    }

    /**
     * Add scripts
     * 
     */

    public function payment_scripts()
    {

      if ('no' === $this->enabled) {
        return;
      };

      if (!isset($_GET['key'])) {
        return;
      };
      $orderKey = $_GET['key'];
      $orderId = wc_get_order_id_by_order_key($orderKey);
      $order = wc_get_order($orderId);
      $orderData = $order->get_data();
      $paymentKey = $order->get_meta('_paymentKey');


      // and this is our custom JS in your plugin directory that works with token.js
      wp_register_script('woow_public', PWG_ROOT_URL . 'dist/checkout/woow.public.iife.js?v=' . uniqid(), [], time(), true);

      // in most payment processors you have to use PUBLIC KEY to obtain a token
      wp_localize_script('woow_public', 'woowParams', array(
        "mainwallet" => $this->get_option('mainwallet'),
        "testwallet" => $this->get_option('testwallet'),
        "testnet" => 'yes' === $this->get_option('testnet'),
        "appName" => $this->get_option('appName'),
        "appLogo" => $this->get_option('appLogo'),
        "allowedTokens" => $this->get_option('allowedTokens'),
        "wooCurrency" => get_woocommerce_currency(),
        "cartAmount" => 20,
        "order" => $orderData,
        "paymentKey" => $paymentKey

      ));

      wp_enqueue_script('woow_public');
      wp_enqueue_style('woow_public_style', PWG_ROOT_URL . 'dist/checkout/woow.public.css?v=' . uniqid());
    }

    /**
     * Register the payment reconciliation key
     */

    public function woow_custom_meta_to_order($order)
    {

      $orderData = $order->get_data();
      $serializedOrder  = serialize($orderData);
      $hashedOrder = hash('sha256', $serializedOrder . time());
      $order->update_meta_data('_paymentKey', $hashedOrder);
      $order->save();
    }

    /**
     * Output for the order received page.
     */
    public function woow_thankyou_page()
    {

      if (isset($this->instructions)) {
        //echo wp_kses_post(wpautop(wptexturize($this->instructions)));
      }
    }


    /**
     * Change payment complete order status to completed for COD orders.
     *
     * @since  3.1.0
     * @param  string         $status Current order status.
     * @param  int            $order_id Order ID.
     * @param  WC_Order|false $order Order object.
     * @return string
     */
    public function woow_change_payment_complete_order_status($status, $order_id = 0, $order = false)
    {
      if ($order && $this->id === $order->get_payment_method()) {
        $status = 'completed';
      }
      return $status;
    }

    /**
     * Add content to the WC emails.
     *
     * @param WC_Order $order Order object.
     * @param bool     $sent_to_admin  Sent to admin.
     * @param bool     $plain_text Email format: plain text or HTML.
     */
    public function woow_email_instructions($order, $sent_to_admin, $plain_text = false)
    {
      if ($this->instructions && !$sent_to_admin && $this->id === $order->get_payment_method()) {
        echo wp_kses_post(wpautop(wptexturize($this->instructions)) . PHP_EOL);
      }
    }

    /**
     * Allow override template
     */
    function woow_relocate_plugin_template($template, $template_name, $template_path)
    {
      global $woocommerce;
      if (!is_wc_endpoint_url('order-received') || empty($_GET['key'])) {
        return $template;
      }
      $order_id = wc_get_order_id_by_order_key($_GET['key']);
      $order = wc_get_order($order_id);

      if ($order->get_payment_method() !== "woow") return $template;

      $_template = $template;
      if (!$template_path)
        $template_path = $woocommerce->template_url;

      $plugin_path  = untrailingslashit(PWG_ROOT_DIR)  . '/includes/woocommerce/templates/';

      // Look within passed path within the theme - this is priority
      $template = locate_template(
        array(
          $template_path . $template_name,
          $template_name
        )
      );

      if (!$template && file_exists($plugin_path . $template_name))
        $template = $plugin_path . $template_name;

      if (!$template)
        $template = $_template;

      return $template;
    }

    public function generate_woow_register_html($key, $data)
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
          <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok. 
                                                                                                        ?></label>
        </th>
        <td class="forminp">
          <fieldset>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
            <div id="proton-store-reg">

            </div>
            <?php echo $this->get_description_html($data); // WPCS: XSS ok. 
            ?>
          </fieldset>
        </td>
      </tr>
    <?php
      return ob_get_clean();
    }

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

    public function woow_add_admin_script()
    {
      global $current_screen;
      if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wc-settings') {
        wp_register_script('woow_admin_regstore', PWG_ROOT_URL . 'dist/regstore/woow.admin.regstore.iife.js?v=' . uniqid(), [], time(), true);
        wp_localize_script('woow_admin_regstore', 'woowRegStoreParams', array(
          "networkCheckBoxSelector" => "#woocommerce_woow_testnet",
          "mainnetAccountFieldSelector" => "#woocommerce_woow_mainwallet",
          "testnetAccountFieldSelector" => "#woocommerce_woow_testwallet",
          "mainnetActor" => $this->get_option('mainwallet'),
          "testnetActor" => $this->get_option('testwallet')
        ));

        wp_enqueue_script('woow_admin_regstore');
        wp_enqueue_style('woow_admin_regstore_style', PWG_ROOT_URL . 'dist/regstore/woow.admin.regstore.css?v=' . uniqid());
      };
    }
    public function woow_custom_orders_list_columns($columns)
    {

      $columns['txid'] = __('Transaction', 'woow'); // title
      return $columns;
    }
  }
}
