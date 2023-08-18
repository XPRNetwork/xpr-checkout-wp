export function generateUnregisterStoreAction(fromActor:string,fromPermission:string) {
  
  return {
    account: "wookey",
    name: 'store.unreg',
    authorization: [{
      actor: fromActor,
      permission: fromPermission
    }],
    data: {
        storeAccount: fromActor,
       
    }
  };
}