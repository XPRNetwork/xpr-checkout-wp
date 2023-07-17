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
    require_once PWG_ROOT_DIR . '/includes/woocommerce/gateway/proton-gateway.php';
  }
}
