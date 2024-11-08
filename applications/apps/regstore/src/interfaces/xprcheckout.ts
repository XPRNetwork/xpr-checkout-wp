type xprcheckout_Actions = {
  "bal.claim": {
    storeAccount:string;
    symbol:string
  },
  "dev.clrbal": {
    storeAccount:string
  },
  "dev.clrpay": {
    
  },
  "dev.clrstore": {
    
  },
  "pay.cancel": {
    storeAccount:string;
    paymentKey:{
    
}
  },
  "pay.refund": {
    storeAccount:string;
    paymentKey:string
  },
  "pay.reg": {
    storeAccount:string;
    buyer:string;
    paymentKey:string
  },
  "store.reg": {
    storeAccount:string
  },
  "store.unreg": {
    storeAccount:string
  }
}

export const xprcheckout = {
  bal_claim:(authorization:Authorization[],data:xprcheckout_Actions['bal.claim']):XPRAction<'bal.claim'>=>({
	account:'xprcheckout',
	name:'bal.claim',
	authorization,
data}),
 dev_clrbal:(authorization:Authorization[],data:xprcheckout_Actions['dev.clrbal']):XPRAction<'dev.clrbal'>=>({
	account:'xprcheckout',
	name:'dev.clrbal',
	authorization,
data}),
 dev_clrpay:(authorization:Authorization[],data:xprcheckout_Actions['dev.clrpay']):XPRAction<'dev.clrpay'>=>({
	account:'xprcheckout',
	name:'dev.clrpay',
	authorization,
data}),
 dev_clrstore:(authorization:Authorization[],data:xprcheckout_Actions['dev.clrstore']):XPRAction<'dev.clrstore'>=>({
	account:'xprcheckout',
	name:'dev.clrstore',
	authorization,
data}),
 pay_cancel:(authorization:Authorization[],data:xprcheckout_Actions['pay.cancel']):XPRAction<'pay.cancel'>=>({
	account:'xprcheckout',
	name:'pay.cancel',
	authorization,
data}),
 pay_refund:(authorization:Authorization[],data:xprcheckout_Actions['pay.refund']):XPRAction<'pay.refund'>=>({
	account:'xprcheckout',
	name:'pay.refund',
	authorization,
data}),
 pay_reg:(authorization:Authorization[],data:xprcheckout_Actions['pay.reg']):XPRAction<'pay.reg'>=>({
	account:'xprcheckout',
	name:'pay.reg',
	authorization,
data}),
 store_reg:(authorization:Authorization[],data:xprcheckout_Actions['store.reg']):XPRAction<'store.reg'>=>({
	account:'xprcheckout',
	name:'store.reg',
	authorization,
data}),
 store_unreg:(authorization:Authorization[],data:xprcheckout_Actions['store.unreg']):XPRAction<'store.unreg'>=>({
	account:'xprcheckout',
	name:'store.unreg',
	authorization,
data}) 
} 
type xprcheckout_Tables = {
  "BalancesTable": {
    key:string;
    contract:string;
    amount:string;
    lastClaim:number
  },
  "PaymentsTable": {
    key:number;
    store:string;
    buyer:string;
    paymentKey:{
    
};
    settlement:string;
    tokenContract:string;
    status:number;
    created:number;
    updated:number
  },
  "StoreTable": {
    store:string;
    blacklisted:boolean
  }
}


    export type Authorization = {
      actor: string;
      permission: "active"|"owner"|string;
  }

    export type XPRAction<A extends keyof (xprcheckout_Actions)>={
      account: 'xprcheckout';
      name: A;
      authorization: Authorization[];
      data: xprcheckout_Actions[A]; 
    }
  
export type Tables<TableName extends keyof (xprcheckout_Tables)> = xprcheckout_Tables[TableName];
export type Actions<ActionName extends keyof (xprcheckout_Actions)> = xprcheckout_Actions[ActionName];
export function xprcheckout_actionParams<ActionName extends keyof (xprcheckout_Actions)>(actionPrams: xprcheckout_Actions[ActionName]):(object|number|string |number[]|string[])[]{return Object.values(actionPrams)}
