<?php 
namespace xprcheckout\utils;

class OrderResolver {

  static function Process ($paymentKey, $network, $actor = null){
    global $wpdb;
    
  $args = array(
    'post_type'      => 'shop_order',
    'post_status'    => 'any',
    'meta_key'       => '_payment_key', // Meta key for paymentKey
    'meta_value'     => $paymentKey,
    'meta_compare'   => '=',
    'posts_per_page' => 1,

  );

  $returnResult = [
    'paymentKey'=>null,
    'transactionId'=> null,
    'payer'=> null,
    'paymentVerified'=> false,
    'currency'=>null,
    'fillRatio'=>null,
    'status'=>null,
    'total'=>null,
    'orderKey'=>null,
    'orderId'=>null,
    'cancelRedirect'=>null,
    'continueRedirect'=>null
  ];

  $ordersQuery = wc_get_orders($args);
  if (!isset($ordersQuery[0]) ) return $returnResult;
  $existingOrder = $ordersQuery[0];
  
  $verifiedPaymentOrder = $existingOrder->get_meta('_verified',true);
  $verifiedPaymentKey = $existingOrder->get_meta('_payment_key',true);
  $verifiedFillRatio = $existingOrder->get_meta('_fill_ratio',true);
  $verifiedTrxId = $existingOrder->get_meta('_tx_id',true);
  $verifiedPayer = $existingOrder->get_meta('_payer',true);
  error_log("Payer !!");
  error_log(print_r($existingOrder->get_meta('_payer',true),1));
  $returnResult = [
    'paymentKey'=>!empty($verifiedPaymentKey) ? $verifiedPaymentKey : null,
    'transactionId'=>!empty($verifiedTrxId) ? $verifiedTrxId : null,
    'payer'=>!empty($verifiedPayer) ? $verifiedPayer : null,
    'paymentVerified'=>!empty($verifiedPaymentOrder) ? boolval($verifiedPaymentOrder) : false,
    'currency'=>$existingOrder->get_currency(),
    'fillRatio'=>!empty($verifiedFillRatio) ? $verifiedFillRatio : null,
    'status'=>$existingOrder->get_status(),
    'total'=>$existingOrder->get_total(),
    'orderKey'=>$existingOrder->get_order_key(),
    'orderId'=>$existingOrder->get_id(),
    'cancelRedirect'=>$existingOrder->get_checkout_payment_url(),
    //'cancelRedirect'=>wc_get_page_id('order-pay'),
    'continueRedirect'=>$existingOrder->get_view_order_url()
  ];

  //return $returnResult;

  
  $orderPaymentKey = $existingOrder->get_meta('_payment_key');
  
  $datetime = new \DateTime($existingOrder->get_date_created());
  $filterAfterDate = $datetime->format(\DateTime::ATOM);
  $rpcEndpoint = $network == 'testnet' ? XPRCHECKOUT_TESTNET_ENDPOINT : XPRCHECKOUT_MAINNET_ENDPOINT;
  $rpc = new \ProtonRPC($rpcEndpoint);
  $isPaymentVerified = $rpc->verifyPaymentStatusByKey($orderPaymentKey);
  if (!$isPaymentVerified)return $returnResult;
  error_log("payment verified");
  error_log("payment verified");
  $orderCurrency = $existingOrder->get_currency();
  $orderCurrencyUSDRate = $existingOrder->get_meta('_fiat_rate');
  $orderTotalAsUSD = $existingOrder->get_total()/$orderCurrencyUSDRate;
  error_log($orderTotalAsUSD.' ??');

  
  if (is_null($verifiedPayer)) return $returnResult;

  $rpcEndpoint = $network == 'testnet' ? 'https://api-xprnetwork-test.saltant.io/' : 'https://api-xprnetwork-main.saltant.io/';
  $rpc = new \ProtonRPC($rpcEndpoint);
  error_log(print_r($verifiedPayer,1));
  $foundTransactions = $rpc->findTransaction($verifiedPayer,$filterAfterDate,$paymentKey);
  $txMatchPayment = null;
  $txQuantity = null;
  $txTokenMatches = null;
  $txToken = null;
  
  if(is_null($foundTransactions)) return $returnResult;
  if(!isset($foundTransactions['actions'])) return $returnResult;
  if (count($foundTransactions['actions'])==0) return $returnResult;

  foreach($foundTransactions['actions'] as $action){  
    if (isset($action['act']) && isset($action['act']['data']) && isset($action['act']['data'])){
      if ($action['act']['data']['memo'] == $orderPaymentKey) {
        $txQuantity = floatval($action['act']['data']['quantity']);
        preg_match('/[A-Z]{3,12}$/',$action['act']['data']['quantity'],$txTokenMatches);
        $txMatchPayment = $action['trx_id'];
        if (isset($txTokenMatches[0]))$txToken = $txTokenMatches[0];
      };
    }
  }
  
  $token = $existingOrder->get_meta('_payment_token');
  $rate = $existingOrder->get_meta('_payment_rate');
  $rate = $existingOrder->get_meta('_payment_rate');
  $convertedPrice = $orderTotalAsUSD/$rate;
  error_log('Order total: 1.0 NZD');
  error_log('USD order total: '.$orderTotalAsUSD);
  $orderFillRatio = ($convertedPrice/$txQuantity);
  error_log('On chain Quantity: '.$txQuantity);
  error_log('converted Token Price: '.$convertedPrice);
  error_log('orderFillRatio: '.$orderFillRatio);
    
  if ($orderFillRatio>1.001){
    error_log('Partial fill');
    $existingOrder->set_status('wookey_add_partial_fill_order_status');
  }else {
    error_log('Total fill');
    $existingOrder->set_status('processing');
  }

  $returnResult['paymentKey'] = $orderPaymentKey;
  $returnResult['transactionId'] = $txMatchPayment;
  $returnResult['payer'] = $actor;
  $returnResult['paymentVerified'] = $isPaymentVerified;
  $returnResult['currency'] = $existingOrder->get_currency();
  $returnResult['fillRatio'] = $orderFillRatio;
  $returnResult['status'] = $existingOrder->get_status();
  $returnResult['total'] = $existingOrder->get_total();
  $returnResult['token'] = $txToken;
  $returnResult['orderKey']=$existingOrder->get_order_key();
  $returnResult['orderId']=$existingOrder->get_id();
  $returnResult['cancelRedirect']=$existingOrder->get_checkout_payment_url();
  $returnResult['continueRedirect']=$existingOrder->get_view_order_url();

  
  $existingOrder->update_meta_data('_payed_tokens',$txQuantity);
  $existingOrder->update_meta_data('_tx_id',$txMatchPayment);
  $existingOrder->update_meta_data('_payer',$actor);
  $existingOrder->update_meta_data('_verified',boolval($isPaymentVerified));
  $existingOrder->update_meta_data('_fill_ratio',$orderFillRatio);
  $existingOrder->save();  
  return $returnResult;
  }

}