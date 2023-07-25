export function generateRegisterStoreAction(fromActor:string,fromPermission:string) {
  
  return {
    account: "woow",
    name: 'store.reg',
    authorization: [{
      actor: fromActor,
      permission: fromPermission
    }],
    data: {
        storeAccount: fromActor,
       
    }
  };
}