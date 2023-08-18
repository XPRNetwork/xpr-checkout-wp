<?php

namespace wookey\admin;

use wookey\config\Config;

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}


/**
 * Represents the WooKey dashboard functionality within the WordPress admin.
 * 
 * This class is responsible for managing the WooKey dashboard interface in the WordPress admin section,
 * including registering necessary scripts and adding menu items for the WooKey dashboard.
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
   * Registers and enqueues scripts for the WooKey dashboard.
   * 
   * This method checks the current screen ID and if it matches the WooKey dashboard, it registers 
   * and enqueues necessary scripts and styles for the dashboard.
   */
  public function registerScript()
  {

    global $current_screen;
    if (isset($current_screen) && $current_screen->id == 'woocommerce_page_wookey-dashboard') {
      wp_enqueue_style('wookey_admin_dashboard_style', WOOKEY_ROOT_URL . 'dist/admin/dashboard/wookey.admin.dashboard.css?v=' . uniqid());
      wp_register_script('wookey_admin_dashboard', WOOKEY_ROOT_URL . 'dist/admin/dashboard/wookey.admin.dashboard.iife.js?v=' . uniqid(), [], time(), true);
      wp_localize_script('wookey_admin_dashboard', 'wookeyDashboardParams', Config::GetBaseConfig());
      wp_enqueue_script('wookey_admin_dashboard');
    };
  }

  /**
   * Adds a submenu item for the WooKey dashboard in the WooCommerce menu.
   */
  public function addMenuItem()
  {
    add_submenu_page('woocommerce', 'Wookey dashboard', 'Wookey dashboard', 'manage_options', 'wookey-dashboard', [$this, 'render']);
  }

  /**
   * Renders the WooKey dashboard interface.
   * 
   * Displays the WooKey dashboard contents. It checks if the WooKey gateway is available and displays 
   * either the main dashboard interface or a misconfiguration warning.
   */
  public function render()

  {

    $wookeyGateway = WC()->payment_gateways->payment_gateways()['wookey'];
    ob_start();
?>
    <div class="wrap ">
      <div id="poststuff">
        <?php if ($wookeyGateway->is_available()) : ?>
          <div id="wookey-payout"></div>
        <?php else : ?>
          <div class="misconfig-warning">
            <h3>Wookey misconfiguration</h3>
            <p>
              Wookey configuration doesn't have registered store account for <?php echo  $wookeyGateway->is_testnet() ? 'testnet' : 'mainnet' ?>.
            </p>
            <a href="<?php echo admin_url('admin.php?page=wc-settings&tab=checkout&section=wookey') ?>" class="button button-warning">Please fix it </a>
          </div>
        <?php endif ?>

      </div>
    </div>
<?php
    echo ob_get_clean();
  }
}
