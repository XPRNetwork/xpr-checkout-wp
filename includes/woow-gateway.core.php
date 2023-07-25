<?php
class ProtonWcGateway
{

  function __construct()
  {
  }

  public function run()
  {
    $this->loadDependencies();
  }

  private function loadDependencies()
  {

    require_once PWG_ROOT_DIR . '/includes/woocommerce/gateway/woow-gateway.php';
    require_once PWG_ROOT_DIR . '/includes/rpc/ProtonRPC.php';
    require_once PWG_ROOT_DIR . '/includes/rpc/PriceRateRPC.php';
    require_once PWG_ROOT_DIR . '/includes/rpc/TokensPricesRPC.php';
    require_once PWG_ROOT_DIR . '/includes/api/payment-check.php';
    require_once PWG_ROOT_DIR . '/includes/api/price-rates.php';
    require_once PWG_ROOT_DIR . '/includes/api/tokens-prices.php';
  }
}
