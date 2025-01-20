<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

use xprcheckout\config\Config;
use xprcheckout\i18n\Translations;

  global $wp_query;
  $g = new XPRCheckoutGateway(); 
  
  $orderPaymentKey = $wp_query->query_vars['paymentKey'];
  $baseConfig = Config::GetConfig($orderPaymentKey);
  
?>

<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head() ?>
<?php
wp_localize_script('xprcheckout-config', 'xprcheckoutConfig', $baseConfig);
wp_enqueue_script('xprcheckout-config', XPRCHECKOUT_ROOT_URL . 'assets/js/xprcheckout-config.js', array(), XPRCHECKOUT_VERSION, true);
?>
<?php $g->payment_scripts(); ?>
  </head>
  <body class="checkout_body <?php echo esc_html(join(" ",get_body_class())) ?>">
  
   
    <div style='max-width:1040px;margin:0 auto' >
      <div id='xpr-checkout'>
        
        </div>  
      </div>
    <?php wp_footer() ?>
  </body>
</html>

