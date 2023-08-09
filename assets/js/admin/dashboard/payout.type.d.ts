import { LinkSession, TransactResult } from "@proton/web-sdk";



export interface PayoutControllerOption {

  testnetActor?:string;
  mainnetActor?:string;
  testnet?: boolean;
  allowedTokens: string;
  wooCurrency: string;
  baseDomain: string;
  
}
  
export interface PayoutState {
  
  appState?:string;
  isRunning:boolean,
  session?:LinkSession
  tx?:TransactResult,
  order?:any
  
}