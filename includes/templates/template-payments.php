<?php 
$g = new XPRCheckoutGateway(); 
$g->payment_scripts();
?>
<?php wp_head() ?>
<?php 
  
  global $wp_query;
  use xprcheckout\config\Config;
  use xprcheckout\i18n\Translations;
 
  $orderPaymentKey = $wp_query->query_vars['paymentKey'];
  
  $baseConfig = Config::GetConfigWithOrder($orderPaymentKey);
  $translations = ["translations" => Translations::getPublicTranslations()];
  wp_localize_script('xprcheckout_public', 'params', array_merge($baseConfig, $translations));
  ?>
    <div style='max-width:1040px;margin:0 auto' >
      <div id='xprcheckout-checkout'>
        
        </div>  
      </div>
    <?php wp_footer() ?>