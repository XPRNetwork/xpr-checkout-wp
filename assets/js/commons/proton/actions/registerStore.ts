export function generateRegisterStoreAction(fromActor:string,fromPermission:string) {
  
  return {
    account: "wookey",
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