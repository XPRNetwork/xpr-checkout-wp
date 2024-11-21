<?php

namespace xprcheckout\admin;

use xprcheckout\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

/**
 * Admin Notices Handler.
 *
 * Handles the display of admin notices for the XPRCheckout payment gateway plugin.
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
   * Shows warning and error notices related to XPRCheckout payment gateway configuration.
   * 
   * Displays notices on the WordPress admin dashboard when the plugin is misconfigured.
   * Specifically, it focuses on issues related to the testnet and mainnet configurations.
   */
  function showWarningNotices()
  {

    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    $mainnetActor = $xprcheckoutGateway->get_option('wallet');
    $isTestnet = $xprcheckoutGateway->get_option('testnet') == 'yes';

    if ($isTestnet) :
?>
      <div class="notice notice-warning">
        <p><b>XPRCheckout is on testnet mode!</b> Don't forget disable testnet mode before going on production</p>
      </div>
    <?php
    endif;

    if ($isTestnet && $testnetActor == "") :
    ?>
      <div class="notice notice-error">
        <p><b>XPRCheckout testnet misconfiguration</b> ! XPRCheckout configuration doesn't have registered store account for testnet. <a href="<?php admin_url('http://wp-admin/admin.php?page=wc-settings&tab=checkout&section=xprcheckout') ?>">Fix it »</a></p>
      </div>
    <?php
    endif;
    if (!$isTestnet && $mainnetActor == "") :
    ?>
      <div class="notice notice-error">
        <p><b>XPRCheckout mainnet misconfiguration</b> ! XPRCheckout configuration doesn't have registered store account for mainnet. <a href="<?php admin_url('admin.php?page=wc-settings&tab=checkout&section=xprcheckout') ?>">Fix it »</a></p>
      </div>
<?php
    endif;
  }
}
