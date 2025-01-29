<?php 

  $g = new XPRCheckoutGateway(); 
  wp_enqueue_script(XPRCHECKOUT_CHECKOUT_APP_HANDLE);
?>

<html>
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head() ?>

<?php $g->payment_scripts(); ?>
  </head>
  <body class="checkout_body <?php echo esc_html(join(" ",get_body_class())) ?>">
    <div style='max-width:1040px;margin:0 auto' >
      <div id='xpr-checkout'></div>  
      </div>
    <?php wp_footer() ?>
  </body>
</html>

