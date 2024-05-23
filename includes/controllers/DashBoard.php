<?php

namespace xprcheckout\admin;

use xprcheckout\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


/**
 * Represents the XPRCheckout dashboard functionality within the WordPress admin.
 * 
 * This class is responsible for managing the XPRCheckout dashboard interface in the WordPress admin section,
 * including registering necessary scripts and adding menu items for the XPRCheckout dashboard.
 */

class Dashboard
{

  /**
   * Dashboard constructor.
   * 
   * Initializes the dashboard and registers the necessary WordPress actions.
   */
  public function __construct()
  {

    $this->registerActions();
  }

  /**
   * Registers WordPress actions.
   * 
   * This method attaches class methods to WordPress action hooks.
   * @access private
   */
  private function registerActions()
  {

    add_action('admin_enqueue_scripts', [$this, 'registerScript']);
    add_action('admin_menu', [$this, 'addMenuItem'], 99);
  }

  /**
   * Registers and enqueues scripts for the XPRCheckout dashboard.
   * 
   * This method checks the current screen ID and if it matches the XPRCheckout dashboard, it registers 
   * and enqueues necessary scripts and styles for the dashboard.
   */
  public function registerScript()
  {

    global $current_screen;
    if (isset($current_screen) && $current_screen->id == 'woocommerce_page_xprcheckout-dashboard') {
      wp_enqueue_style('xprcheckout_admin_dashboard_style', XPRCHECKOUT_ROOT_URL . 'dist/admin/dashboard/xprcheckout.admin.dashboard.css?v=' . uniqid());
      wp_register_script('xprcheckout_admin_dashboard', XPRCHECKOUT_ROOT_URL . 'dist/admin/dashboard/xprcheckout.admin.dashboard.iife.js?v=' . uniqid(), [], time(), true);
      wp_localize_script('xprcheckout_admin_dashboard', 'xprcheckoutDashboardParams', Config::GetDashbordConfig());
      wp_enqueue_script('xprcheckout_admin_dashboard');
    };
  }

  /**
   * Adds a submenu item for the XPRCheckout dashboard in the WooCommerce menu.
   */
  public function addMenuItem()
  {
    add_submenu_page('woocommerce', 'XPRCheckout dashboard', 'XPRCheckout dashboard', 'manage_options', 'xprcheckout-dashboard', [$this, 'render']);
  }

  /**
   * Renders the XPRCheckout dashboard interface.
   * 
   * Displays the XPRCheckout dashboard contents. It checks if the XPRCheckout gateway is available and displays 
   * either the main dashboard interface or a misconfiguration warning.
   */
  public function render()

  {

    $xprcheckoutGateway = WC()->payment_gateways->payment_gateways()['xprcheckout'];
    ob_start();
?>
    <div class="wrap ">
      <div id="poststuff">
        <?php if ($xprcheckoutGateway->is_available()) : ?>
          <div id="xprcheckout-payout"></div>
        <?php else : ?>
          <div class="misconfig-warning">
            <h3>XPRCheckout misconfiguration</h3>
            <p>
              XPRCheckout configuration doesn't have registered store account for <?php echo  $xprcheckoutGateway->is_testnet() ? 'testnet' : 'mainnet' ?>.
            </p>
            <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=xprcheckout') ?>" class="button button-warning">Please fix it </a>
          </div>
        <?php endif ?>

      </div>
    </div>
<?php
    echo ob_get_clean();
  }
}
