<?php
class WoowDashboadLayout
{

  function  __construct($options = [])
  {

    $this->options = $options;
  }

  public function render()
  {

    ob_start();
?>
    <div class="wrap ">
      <div id="poststuff">
        <div id="post-body" class="woow-dashboard">
          <div id="postbox-container-2">
            <?php echo $this->renderPayments()  ?>
          </div>
          <div id="postbox-container-1">
            <?php echo $this->renderBalances() ?>
          </div>


        </div>
        <br class="clear">
      </div>
    </div>
  <?php
    return ob_get_clean();
  }

  private function renderPayments()
  {

    $protonRPC = new ProtonRPC("https://test.proton.eosusa.io");
    $payments = $protonRPC->fetchPayments('solid');

    ob_start();
  ?>
    <div>
      <h3>Payments</h3>

      <table class="wp-list-table widefat fixed striped table-view-list posts">
        <thead>
          <td>Order</td>
          <td>Status</td>
          <td>Total</td>
        </thead>
        <tbody>
          <?php foreach ($payments['rows'] as $row) : ?>
            <tr>
              <td>
                <?php
                $args = array(
                  'post_type'      => 'shop_order',
                  'post_status'    => 'any',
                  'meta_key'       => '_paymentKey', // Meta key for paymentKey
                  'meta_value'     => $row['paymentKey'],
                  'meta_compare'   => '=',
                  'posts_per_page' => 1,
                );

                $ordersQuery = wc_get_orders($args);
                if ($ordersQuery && isset($ordersQuery[0])) {
                  $url = admin_url('post.php?post=' . $ordersQuery[0]->get_id()) . '&action=edit';
                  $orderLabel = "#" . $ordersQuery[0]->get_id() . " " . $row['buyer'];
                  echo '<a href="' . $url . '"> <b>' . $orderLabel . '</b> </a>';
                }
                ?>
              </td>
              <td>
                <?php
                //TODO: Rely on CSS class 
                $row['status'];
                switch ($row['status']) {

                  case -2:
                    $bgColor = "#e1c6d4";
                    $text = "Refunded";
                    break;

                  case -1:
                    $bgColor = "#ded5aa";
                    $text = "Canceled";
                    break;

                  case 0:
                    $bgColor = "#c8d7e1";
                    $text = "Await";
                    break;

                  case 1:
                    $bgColor = "#c6e1c6";
                    $text = "Fulfilled";
                    break;

                  case 2:
                    $bgColor = "#c6e1c6";
                    $text = "Payed out";
                    break;
                }
                echo '<span class="button-primary" style="color:#50575e;background-color:' . $bgColor . ';" >' . $text . '</span>';
                ?>
              </td>
              <td><?php echo $row['amount'] ?></td>

            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php
    return ob_get_clean();
  }

  private function renderBalances()
  {

    $protonRPC = new ProtonRPC("https://test.proton.eosusa.io");
    $balances = $protonRPC->fetchBalances('solid');
    ob_start();

  ?>
    <div>
      <h3>Payout</h3>
      <table class="wp-list-table widefat fixed striped table-view-list posts">

        <tbody>
          <?php foreach ($balances['rows'] as $row) : ?>
            <tr>
              <td>
                <?php echo $row['amount'] ?>
              </td>

              <td>
                <a class="button-primary">Widthdraw</a>
              </td>

            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
<?php
    return ob_get_clean();
  }
}
