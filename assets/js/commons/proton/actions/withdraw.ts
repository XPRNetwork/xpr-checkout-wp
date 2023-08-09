export function generateWithdrawAction(fromActor:string,fromPermission:string, symbol: number) {
  
  return {
    account: "wookey",
    name: 'bal.claim',
    authorization: [{
      actor: fromActor,
      permission: fromPermission
    }],
    data: {
        storeAccount: fromActor,
        symbol:`${symbol}`
    }
  };
}