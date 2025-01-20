<?php
class XPRCheckout_WcGateway
{

  function __construct()
  {
  }

  public function run()
  {
    $this->loadDependencies();
    $this->bootstrap();
    
  }

  private function loadDependencies()
  {

    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Cart.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Config.php';
    
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Gateway.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/i18n.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Notices.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Orders.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/Refund.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/controllers/RegStore.php';

    require_once XPRCHECKOUT_ROOT_DIR . 'includes/rpc/XPRCheckout_ProtonRPC.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/rpc/XPRCheckout_PriceRateRPC.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/rpc/TokensPricesRPC.php';
    
    
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/api/order-payment.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/api/admin-refund.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/api/admin-save-wallet.php';

    require_once XPRCHECKOUT_ROOT_DIR . 'includes/utils/order-resolver.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/utils/symbol.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/utils/to-precision.php';
    require_once XPRCHECKOUT_ROOT_DIR . 'includes/utils/order-by-payment-key.php';
    
    
  }

  private function bootstrap()
  {

    $gateway    = new \xprcheckout\gateway\GatewayWrapper();
    
    $refund     = new \xprcheckout\admin\Refund();
    $regStore     = new \xprcheckout\admin\RegStore();
    
    $orders     = new \xprcheckout\admin\Orders();
    $notices    = new \xprcheckout\admin\Notices();
    
    
  }
}
