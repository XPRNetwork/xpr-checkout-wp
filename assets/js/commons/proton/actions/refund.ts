export function generateRefundAction(fromActor:string,fromPermission:string,paymentKey:String) {
  
  return {
    account: "wookey",
    name: 'pay.refund',
    authorization: [{
      actor: fromActor,
      permission: fromPermission
    }],
    data: {
      storeAccount: fromActor,
      paymentKey:paymentKey
       
    }
  };
}