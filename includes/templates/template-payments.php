<?php 
$g = new XPRCheckoutGateway(); 

use xprcheckout\config\Config;
  use xprcheckout\i18n\Translations;
 
  $orderPaymentKey = $wp_query->query_vars['paymentKey'];
  $baseConfig = Config::GetConfig($orderPaymentKey);
  
?>
<script type='text/javascript'>

window.pluginConfig = <?php echo json_encode($baseConfig); ?>;

</script>
<?php 
$g->payment_scripts();
?>
<?php wp_head() ?>
<?php 
  
  global $wp_query;
  

  
  ?>
    <div style='max-width:1040px;margin:0 auto' >
      <div id='root'>
        
        </div>  
      </div>
    <?php wp_footer() ?>