import {LinkSession,TransactResult } from '@proton/web-sdk'
export interface TokenRate{
  contract: string
  decimals: number
  symbol: string
  id: string
  logo?: string;
  quote: {
    price_usd:number
  }
}

export interface ProtonWCControllerOption {

  mainwallet?:string;
  testwallet?:string;
  testnet?:boolean;
  appName?:string;
  appLogo?:string;
  allowedTokens?:string;
  wooCurrency?:string;
  paymentKey:string;
  order:{
    total:number,
    status:string
  }
  
  }
  
  export interface ProtonCheckOutState {
  
  appState?:string;
  isRunning:boolean,
  session?:LinkSession
  tx?:TransactResult,
  order?:any
  
  }