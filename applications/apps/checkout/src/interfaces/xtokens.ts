type xtokens_Actions = {
  "close": {
    owner:string;
    symbol:string
  },
  "create": {
    issuer:string;
    maximum_supply:string
  },
  "issue": {
    to:string;
    quantity:string;
    memo:string
  },
  "open": {
    owner:string;
    symbol:string;
    ram_payer:string
  },
  "retire": {
    quantity:string;
    memo:string
  },
  "transfer": {
    from:string;
    to:string;
    quantity:string;
    memo:string
  }
}

export const xtokens = {
  close:(authorization:Authorization[],data:xtokens_Actions['close']):XPRAction<'close'>=>({
	account:'xtokens',
	name:'close',
	authorization,
data}),
 create:(authorization:Authorization[],data:xtokens_Actions['create']):XPRAction<'create'>=>({
	account:'xtokens',
	name:'create',
	authorization,
data}),
 issue:(authorization:Authorization[],data:xtokens_Actions['issue']):XPRAction<'issue'>=>({
	account:'xtokens',
	name:'issue',
	authorization,
data}),
 open:(authorization:Authorization[],data:xtokens_Actions['open']):XPRAction<'open'>=>({
	account:'xtokens',
	name:'open',
	authorization,
data}),
 retire:(authorization:Authorization[],data:xtokens_Actions['retire']):XPRAction<'retire'>=>({
	account:'xtokens',
	name:'retire',
	authorization,
data}),
 transfer:(authorization:Authorization[],data:xtokens_Actions['transfer']):XPRAction<'transfer'>=>({
	account:'xtokens',
	name:'transfer',
	authorization,
data}) 
} 
type xtokens_Tables = {
  "account": {
    balance:string
  },
  "currency_stats": {
    supply:string;
    max_supply:string;
    issuer:string
  }
}


    export type Authorization = {
      actor: string;
      permission: "active"|"owner"|string;
  }

    export type XPRAction<A extends keyof (xtokens_Actions)>={
      account: 'xtokens';
      name: A;
      authorization: Authorization[];
      data: xtokens_Actions[A]; 
    }
  
export type Tables<TableName extends keyof (xtokens_Tables)> = xtokens_Tables[TableName];
export type Actions<ActionName extends keyof (xtokens_Actions)> = xtokens_Actions[ActionName];
export function xtokens_actionParams<ActionName extends keyof (xtokens_Actions)>(actionPrams: xtokens_Actions[ActionName]):(object|number|string |number[]|string[])[]{return Object.values(actionPrams)}
