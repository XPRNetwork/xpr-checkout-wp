<?php
class ProtonWcGateway
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

    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Cart.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Config.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/DashBoard.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Gateway.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/i18n.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Notices.php';
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Orders.php';
    
    require_once WOOKEY_ROOT_DIR . 'includes/controllers/Refund.php';

    require_once WOOKEY_ROOT_DIR . 'includes/rpc/ProtonRPC.php';
    require_once WOOKEY_ROOT_DIR . 'includes/rpc/PriceRateRPC.php';
    require_once WOOKEY_ROOT_DIR . 'includes/rpc/TokensPricesRPC.php';

    require_once WOOKEY_ROOT_DIR . 'includes/api/payment-check.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/verify-transaction.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/price-rates.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/tokens-prices.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/cart.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/update-order.php';
    require_once WOOKEY_ROOT_DIR . 'includes/api/admin-payments-with-orders.php';

    require_once WOOKEY_ROOT_DIR . 'includes/utils/order-resolver.php';
  }

  private function bootstrap()
  {

    $gateway    = new \wookey\gateway\GatewayWrapper();
    $dashboard  = new \wookey\admin\Dashboard();
    $refund     = new \wookey\admin\Refund();
    
    $orders     = new \wookey\admin\Orders();
    $notices    = new \wookey\admin\Notices();
    $cart       = new \wookey\cart\Cart();
    
  }
}
