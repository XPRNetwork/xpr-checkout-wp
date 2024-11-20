<?php 
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
  <script type='text/javascript'>

window.pluginConfig = <?php echo wp_json_encode($baseConfig); ?>;

</script>
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

