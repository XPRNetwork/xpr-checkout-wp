<?php

namespace wookey\admin;

use wookey\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Admin Notices Handler.
 *
 * Handles the display of admin notices for the Wookey payment gateway plugin.
 * This class focuses on warning and error notices related to the configuration of the plugin.
 * 
 */
class Notices
{

  /**
   * Notices constructor.
   * 
   * Initializes the notices handler and registers necessary actions.
   */
  public function __construct()
  {

    $this->registerActions();
  }

  /**
   * Registers WordPress actions.
   * 
   * Attaches methods to WordPress action hooks.
   * @access private
   */
  private function registerActions()
  {

    add_action('admin_notices', [$this, 'showWarningNotices']);
  }

  /**
   * Shows warning and error notices related to Wookey payment gateway configuration.
   * 
   * Displays notices on the WordPress admin dashboard when the plugin is misconfigured.
   * Specifically, it focuses on issues related to the testnet and mainnet configurations.
   */
  function showWarningNotices()
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
}
