export function generateRegisterPaymentAction(storeAccout:string,buyer: string,buyerPermission:string, paymentKey:string,amount:number | string,symbol:string,tokenContract:string) {
  
  return {
    account: "wookey",
    name: 'pay.reg',
    authorization: [{
      actor: buyer,
      permission: buyerPermission
    }],
    data: {
      storeAccount:storeAccout,
      buyer: buyer,
      paymentKey: paymentKey,
      amount: `${amount} ${symbol}`,
      tokenContract: tokenContract
    }
  };
}