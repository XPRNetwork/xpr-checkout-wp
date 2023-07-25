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
    <div>
      <div>
        <h4>Payments</h4>
        <?php echo $this->renderPayments()  ?>
      </div>
      <div>
        <h4>Payouts</h4>
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
    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <td>Amount</td>
        <td>Buyer</td>
        <td>Payment key</td>
        <td>Status</td>
      </thead>
      <tbody>
        <?php foreach ($payments['rows'] as $row) : ?>
          <tr>
            <td><?php echo $row['amount'] ?></td>
            <td><?php echo $row['buyer'] ?></td>
            <td><?php echo $row['paymentKey'] ?></td>
            <td><?php echo $row['status'] ?></td>
          </tr>
        <?php endforeach ?>
      </tbody>
    </table>
<?php
    return ob_get_clean();
  }
}
