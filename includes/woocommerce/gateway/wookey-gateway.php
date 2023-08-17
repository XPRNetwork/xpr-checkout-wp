<?php

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Cash on Delivery Gateway.
 *
 * Provides a Webauth Payment Gateway for your customer.
 *
 * @class       WC_WookeyGateway
 * @extends     WC_Payment_Gateway
 * @version     2.1.0
 * @package     WooCommerce\Classes\Payment
 */

add_action('admin_notices', 'wookey_show_warning');
function wookey_show_warning()
{

  $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
  $mainnetActor = $wookeyGateway->get_option('mainwallet');
  $testnetActor = $wookeyGateway->get_option('testwallet');
  $isTestnet = $wookeyGateway->get_option('testnet') == 'yes';

  if ($isTestnet) :
?>
    <div class="notice notice-warning">
      <p><b>Wookey is on testnet mode!</b> Don't forget disable testnet mode before going on production</p>
    </div>
  <?php
  endif;

  if ($isTestnet && $testnetActor == "") :
  ?>
    <div class="notice notice-error">
      <p><b>Wookey testnet misconfiguration</b> ! Wookey configuration doesn't have registered store account for testnet. <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=wookey') ?>">Fix it »</a></p>
    </div>
  <?php
  endif;
  if (!$isTestnet && $mainnetActor == "") :
  ?>
    <div class="notice notice-error">
      <p><b>Wookey mainnet misconfiguration</b> ! Wookey configuration doesn't have registered store account for mainnet. <a href="<?php admin_url('admin.php?page=wc-settings&tab=checkout&section=wookey') ?>">Fix it »</a></p>
    </div>
  <?php
  endif;
}

add_action('admin_enqueue_scripts', 'wookey_add_styles');
function wookey_add_styles()
{
  global $current_screen;
  if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wookey-dashboard') {
    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    $mainnetActor = $wookeyGateway->get_option('mainwallet');
    $testnetActor = $wookeyGateway->get_option('testwallet');
    wp_enqueue_style('wookey_admin_dashboard_style', WOOKEY_ROOT_URL . 'dist/admin/dashboard/wookey.admin.dashboard.css?v=' . uniqid());
    wp_register_script('wookey_admin_dashboard', WOOKEY_ROOT_URL . 'dist/admin/dashboard/wookey.admin.dashboard.iife.js?v=' . uniqid(), [], time(), true);
    wp_localize_script('wookey_admin_dashboard', 'wookeyDashboardParams', array(
      "mainnetActor" => $mainnetActor,
      "testnetActor" => $testnetActor,
      "testnet" => 'yes' === $wookeyGateway->get_option('testnet'),
      "allowedTokens" => $wookeyGateway->get_option('allowedTokens'),
      "wooCurrency" => get_woocommerce_currency(),
      "baseDomain" => get_site_url(),
    ));

    wp_enqueue_script('wookey_admin_dashboard');
  };
  if (isset($current_screen) && $current_screen->id == 'shop_order') {
    global $post;
    $order = wc_get_order($post->ID);
    $orderData = $order->get_data();
    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    $mainnetActor = $wookeyGateway->get_option('mainwallet');
    $testnetActor = $wookeyGateway->get_option('testwallet');

    wp_enqueue_style('wookey_admin_refund_style', WOOKEY_ROOT_URL . 'dist/admin/refund/wookey.admin.refund.css?v=' . uniqid());
    wp_register_script('wookey_admin_refund', WOOKEY_ROOT_URL . 'dist/admin/refund/wookey.admin.refund.iife.js?v=' . uniqid(), [], time(), true);
    wp_localize_script('wookey_admin_refund', 'wookeyRefundParams', array(
      "mainnetActor" => $mainnetActor,
      "testnetActor" => $testnetActor,
      "testnet" => 'yes' === $wookeyGateway->get_option('testnet'),
      "allowedTokens" => $wookeyGateway->get_option('allowedTokens'),
      "wooCurrency" => get_woocommerce_currency(),
      "baseDomain" => get_site_url(),
      "transactionId" => $order->get_meta('_transactionId'),
      "network" => $order->get_meta('_net'),
      "paymentKey" => $order->get_meta('_paymentKey'),
      "order" => $orderData
    ));

    wp_enqueue_script('wookey_admin_refund');
  };
}

add_action('admin_menu', 'wookey_register_wookey_dashboard', 99);
function wookey_register_wookey_dashboard()
{
  add_submenu_page('woocommerce', 'wookey dashboard', 'wookey dashboard', 'manage_options', 'wookey-dashboard', 'render_wookey_dashboard');
}
function render_wookey_dashboard()
{
  include_once WOOKEY_ROOT_DIR . 'includes/woocommerce/layouts/wookey-dashboard.php';
  $dashboardLayout = new wookeyDashboadLayout();
  echo $dashboardLayout->render();
}

add_filter('woocommerce_payment_gateways', 'wookey_add_gateway_class');
function wookey_add_gateway_class($gateways)
{

  $gateways[] = 'WC_WookeyGateway';
  return $gateways;
}
add_filter('manage_edit-shop_order_columns', 'wookey_custom_orders_list_columns', 11);
function wookey_custom_orders_list_columns($columns)
{

  $new_columns = array();
  foreach ($columns as $column_name => $column_info) {
    $new_columns[$column_name] = $column_info;
    if ('order_status' === $column_name) {
      $new_columns['transactionId'] = __('Transaction', 'wookey'); // title
      $new_columns['net'] = __('Mainnet/testnet', 'wookey'); // title
    }
  }
  return $new_columns;
}


add_action('manage_shop_order_posts_custom_column', 'wookey_custom_orders_list_column_content', 20, 2);
function wookey_custom_orders_list_column_content($column)
{

  global $post;
  if ('transactionId' === $column) {
    $order = wc_get_order($post->ID);
    $transactionId = $order->get_meta('_transactionId');
    $net = $order->get_meta('_net');
    $color = $net == "mainnet" ? "#7cc67c" : "#f1dd06";
    $link = $net == "mainnet" ? "https://protonscan.io/transaction/" : "https://testnet.protonscan.io/transaction/";
    if ($order->get_payment_method()) {
      echo '<a class="button-primary" style="color:#50575e;background-color:' . $color . ';" target="_blank" href="' . $link . $transactionId . '">' . substr($transactionId, strlen($transactionId) - 8, strlen($transactionId)) . '</a>';
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

function wookey_order_display_meta_data($post)
{

  $order = wc_get_order($post->ID);
  if ($order->get_payment_method() !== "wookey") return;
  $blockExplorer = $order->get_meta('_net') == 'testnet' ? WOOKEY_TESTNET_BLOCK_EXPLORER : WOOKEY_MAINNET_BLOCK_EXPLORER;
  ?>
  <div id="wookey-refund"></div>
  <?php

}

function wookey_register_metabox()
{
  add_meta_box(
    'woocommerce-wookey-payment',
    __('Wookey payment', 'wookey'),
    'wookey_order_display_meta_data',
    'shop_order',
    'advanced',
    'core'

  );
}
add_action('add_meta_boxes', 'wookey_register_metabox');


add_action('woocommerce_cart_totals_before_order_total', 'generate_wookey_car_hash', 99, 2);
function generate_wookey_car_hash($cart_item_data)
{

  $cart = WC()->cart->get_cart();
  $serializedCart = wp_json_encode($cart);
  $newHash = $cart ? hash('sha256', $serializedCart . time()) : '';
  WC()->session->set('paymentKey', $newHash);
}

add_action('plugins_loaded', 'wookey_init_translations');
function wookey_init_translations()
{
  load_plugin_textdomain('wookey', FALSE, WOOKEY_ROOT_DIR . 'i18n/languages');
}

function debug_load_textdomain($domain, $mofile)
{
  //echo "Trying ", $domain, " at ", $mofile, "<br />\n";
}
add_action('load_textdomain', 'debug_load_textdomain', 2, 2);


add_action('plugins_loaded', 'wookey_init_gateway_class');
function wookey_init_gateway_class()
{
  class WC_WookeyGateway extends WC_Payment_Gateway
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
      //add_filter('woocommerce_locate_template', array($this, 'wookey_relocate_plugin_template'), 1, 3);
      add_action('woocommerce_checkout_create_order', array($this, 'wookey_custom_meta_to_order'), 20, 1);
      add_action('wp_enqueue_scripts', array($this, 'payment_scripts'));
      add_action('admin_enqueue_scripts', array($this, 'wookey_add_admin_script'));
      // Customer Emails.
      add_action('woocommerce_email_before_order_table', array($this, 'wookey_email_instructions'), 10, 3);
      add_filter('woocommerce_admin_order_should_render_refunds', array($this, 'wookey_disable_refund'), 99, 2);
    }

    /**
     * Setup general properties for the gateway.
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
     * Initialise Gateway Settings Form Fields.
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
          'type' => 'text',
          'description' => __('This controls the title which the user sees during checkout.', 'wookey'),
          'default' => __('Pay securly with with multiple crypto currencies through WebAuth with NO GAS FEE BABY !', 'wookey'),
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
     * Check If The Gateway Is Available For Use.
     *
     * @return bool
     */
    public function is_available()
    {
      $mainwallet = $this->get_option('mainwallet');
      $testwallet = $this->get_option('testwallet');
      $testnet = 'yes' === $this->get_option('testnet');
      if ($testnet && $testwallet == "") return false;
      if (!$testnet && $mainwallet == "") return false;
      return parent::is_available();
    }

    public function is_testnet()
    {
      return  'yes' === $this->get_option('testnet');
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
      error_log("can process the payment at this point");
      error_log(print_r($order, 1));
      error_log("is the payment key in session ?");
      error_log(WC()->session->get('paymentKey'));
      error_log(WC()->session->get('transactionId'));

      $order->update_meta_data('_paymentKey', WC()->session->get('paymentKey'));
      $order->update_meta_data('_transactionId', WC()->session->get('transactionId'));

      if ($order->get_total() > 0) {
        // Mark as processing or on-hold (payment won't be taken until delivery).
        $order->update_status(apply_filters('woocommerce_cod_process_payment_order_status', $order->has_downloadable_item() ? 'on-hold' : 'processing', $order), __('Payment to be made upon delivery.', 'wookey'));
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

      if (!$this->is_available()) return;
      if ($this->description) {
        // you can instructions for test mode, I mean test card numbers etc.
        $desc = $this->description;
        if ($this->testnet) {
          $desc = ' <b>TESTNET ENABLED.</b><br>';
          $desc .= $this->description;
          $desc  = trim($desc);
        }
        echo wpautop('<div id="wookey-checkout"></div>');
        // display the description with <p> tags etc.
        echo wpautop(wp_kses_post($desc));
      }
    }

    /**
     * Register the payment reconciliation key
     */

    public function wookey_custom_meta_to_order($order)
    {

      $cartHash = WC()->session->get('paymentKey');
      $order->update_meta_data('_paymentKey', $cartHash);
      $order->save();
    }



    /**
     * Add scripts
     * 
     */

    public function payment_scripts()
    {

      global $woocommerce;
      error_log('before hash');
      $cart = $woocommerce->cart;
      if ('no' === $this->enabled) {
        return;
      };

      if (!$this->is_available()) return;

      /*if (!isset($_GET['key'])) {
        return;
      };*/
      /*$orderKey = $_GET['key'];
      $orderId = wc_get_order_id_by_order_key($orderKey);
      $order = wc_get_order($orderId);
      $orderData = $order->get_data();
      $paymentKey = $order->get_meta('_paymentKey');*/
      $paymentKey = WC()->session->get('paymentKey');
      // and this is our custom JS in your plugin directory that works with token.js
      wp_register_script('wookey_public', WOOKEY_ROOT_URL . 'dist/public/checkout/wookey.public.iife.js?v=' . uniqid(), [], time(), true);

      $cart =
        // in most payment processors you have to use PUBLIC KEY to obtain a token
        wp_localize_script('wookey_public', 'wookeyCheckoutParams', array(
          "mainwallet" => $this->get_option('mainwallet'),
          "testwallet" => $this->get_option('testwallet'),
          "testnet" => 'yes' === $this->get_option('testnet'),
          "appName" => $this->get_option('appName'),
          "appLogo" => $this->get_option('appLogo'),
          "allowedTokens" => $this->get_option('allowedTokens'),
          "wooCurrency" => get_woocommerce_currency(),
          "cartSession" => [
            "amount" => $cart->total,
            "paymentKey" => $paymentKey,
          ],
          "baseDomain" => get_site_url(),
          "translations" => [
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
            "verifyPaymentDialogTitle" => __("Payment verification", 'wookey'),
            "verifyPaymentDialogText" => __("Please wait while we check payment information.", 'wookey'),
            "verifyPaymentDialogProcessLabel" => __("Verifying payment", 'wookey'),
            "verifySuccessPaymentDialogTitle" => __("Payment verified", 'wookey'),
            "verifySuccessPaymentDialogText" => __("Great, your payment has be verified, order is now completed! ", 'wookey'),
          ]
        ));

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
     * Allow override template
     */
    function wookey_relocate_plugin_template($template, $template_name, $template_path)
    {
      global $woocommerce;
      if (!is_wc_endpoint_url('order-received') || empty($_GET['key'])) {
        return $template;
      }
      $order_id = wc_get_order_id_by_order_key($_GET['key']);
      $order = wc_get_order($order_id);

      if ($order->get_payment_method() !== "wookey") return $template;

      $_template = $template;
      if (!$template_path)
        $template_path = $woocommerce->template_url;

      $plugin_path  = untrailingslashit(WOOKEY_ROOT_DIR)  . '/includes/woocommerce/templates/';

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
          <label for="<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok. 
                                                                                                        ?></label>
        </th>
        <td class="forminp">
          <fieldset>
            <legend class="screen-reader-text"><span><?php echo wp_kses_post($data['title']); ?></span></legend>
            <div id="wookey-regstore">

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

    public function wookey_add_admin_script()
    {
      global $current_screen;


      if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wc-settings') {
        wp_register_script('wookey_admin_regstore', WOOKEY_ROOT_URL . 'dist/admin/regstore/wookey.admin.regstore.iife.js?v=' . uniqid(), [], time(), true);
        wp_localize_script('wookey_admin_regstore', 'wookeyRegStoreParams', array(
          "networkCheckBoxSelector" => "#woocommerce_wookey_testnet",
          "mainnetAccountFieldSelector" => "#woocommerce_wookey_mainwallet",
          "testnetAccountFieldSelector" => "#woocommerce_wookey_testwallet",
          "mainnetActor" => $this->get_option('mainwallet'),
          "testnetActor" => $this->get_option('testwallet')
        ));

        wp_enqueue_script('wookey_admin_regstore');
        wp_enqueue_style('wookey_admin_regstore_style', WOOKEY_ROOT_URL . 'dist/admin/regstore/wookey.admin.regstore.css?v=' . uniqid());
      };
    }
    public function wookey_custom_orders_list_columns($columns)
    {

      $columns['transactionId'] = __('Transaction', 'wookey'); // title
      return $columns;
    }

    public function wookey_disable_refund($enableRefund, $order)
    {

      $order = wc_get_order($order);
      if ($order->get_payment_method() == "wookey") return false;
      return $enableRefund;
    }
  }
}

add_action('wp_loaded', 'maybe_load_cart', 5);
/**
 * Loads the cart, session and notices should it be required.
 *
 * Note: Only needed should the site be running WooCommerce 3.6
 * or higher as they are not included during a REST request.
 *
 * @see https://plugins.trac.wordpress.org/browser/cart-rest-api-for-woocommerce/trunk/includes/class-cocart-init.php#L145
 * @since   2.0.0
 * @version 2.0.3
 */
function maybe_load_cart()
{
  if (version_compare(WC_VERSION, '3.6.0', '>=') && WC()->is_rest_api_request()) {


    require_once WC_ABSPATH . 'includes/wc-cart-functions.php';
    require_once WC_ABSPATH . 'includes/wc-notice-functions.php';

    if (null === WC()->session) {
      $session_class = apply_filters('woocommerce_session_handler', 'WC_Session_Handler'); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound

      // Prefix session class with global namespace if not already namespaced
      if (false === strpos($session_class, '\\')) {
        $session_class = '\\' . $session_class;
      }

      WC()->session = new $session_class();
      WC()->session->init();
    }

    /**
     * For logged in customers, pull data from their account rather than the
     * session which may contain incomplete data.
     */
    if (is_null(WC()->customer)) {
      if (is_user_logged_in()) {
        WC()->customer = new WC_Customer(get_current_user_id());
      } else {
        WC()->customer = new WC_Customer(get_current_user_id(), true);
      }

      // Customer should be saved during shutdown.
      add_action('shutdown', array(WC()->customer, 'save'), 10);
    }

    // Load Cart.
    if (null === WC()->cart) {
      WC()->cart = new WC_Cart();
    }
  }
} // END maybe_load_cart()
