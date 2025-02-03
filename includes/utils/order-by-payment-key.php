<?php 
if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}
function xprcheckout_get_order_by_payment_key ($paymentKey){
  $args = array(
    'post_type'      => 'shop_order',
    'post_status'    => 'any',
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
    'meta_key'       => '_payment_key', // Meta key for paymentKey
    // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
    'meta_value'     => $paymentKey,
    'meta_compare'   => '=',
    'posts_per_page' => 1,
);

  $ordersQuery = wc_get_orders($args);
  $existingOrder = $ordersQuery[0] ?? null;
  return $existingOrder;
}

?>