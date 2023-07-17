export function generateTransferAction(contract:string,fromActor:string,fromPermission:string,toActor:string,amount: number, symbol: string,memo='') {
  
  return [{
    account: contract,
    name: 'transfer',
    authorization: [{
      actor: fromActor,
      permission: fromPermission
    }],
    data: {
        from: fromActor,
        to: toActor,
        quantity: `${amount} ${symbol}` ,
        memo: memo
    }
  }];
}