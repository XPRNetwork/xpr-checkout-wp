<?php 
namespace xprcheckout\utils;

class OrderResolver {

  static function Process ($paymentKey,$convertedTokens,$network){


    $baseResponse = new \stdClass();
    $baseResponse->resolved = false;
    $baseResponse->payment = null;

    $rpcEndPoint = $network=='mainnet' ? XPRCHECKOUT_MAINNET_ENDPOINT : XPRCHECKOUT_TESTNET_ENDPOINT;
  $rpc = new \XPRCheckout_ProtonRPC($rpcEndPoint);
  $resolved = $rpc->verifyPaymentStatusByKey($paymentKey);
  if ($resolved){

    $payment = $rpc->fetchPayment($paymentKey);
    if (is_null($payment))return false;
    $baseResponse->payment=$payment;
    $settlementPart = explode(" ",$payment['settlement']);
    $convertedMatch = array_search($settlementPart[1], array_column($convertedTokens, 'symbol'));
    if ($convertedMatch===false){
      return false;
    }
    $matchLocalSettlement = $convertedTokens[$convertedMatch];
    if (floatval($matchLocalSettlement->amount) != floatval($settlementPart[0])){
      if ($convertedMatch===false){   
        return $false;
      }
    }
    $baseResponse->resolved= true;
        
  }else {
    $baseResponse->resolved= false;
    
  }
  return $baseResponse;
  }
}