import { LinkSession, TransactResult } from "@proton/web-sdk";



export interface RefundControllerOption {

  testnetActor?:string;
  mainnetActor?:string;
  testnet?: boolean;
  wooCurrency: string;
  order: any;
  network: string;
  transactionId: string;
  paymentKey: string;
  
  
}
  
export interface RefundState {
  
  appState?:string;
  isRunning:boolean,
  session?:LinkSession
  order?:any
  
}