import { LinkSession, TransactResult } from "@proton/web-sdk";

export interface Translation {

  payInviteTitle: string;
  payInviteText: string;
  payInviteButtonLabel: string;

  orderStatusTitle: string;
  orderStatusText: string;

  selectTokenDialogTitle: string;
  selectTokenDialogText: string;
  selectTokenDialogConnectedAs: string;
  selectTokenDialogChangeAccountLabel: string;
  selectTokenPayButtonLabel: string;
  selectTokenPayProcessingLabel: string;
  
  verifyPaymentDialogTitle: string;
  verifyPaymentDialogText: string;
  verifyPaymentDialogProcessLabel: string;
  
  verifySuccessPaymentDialogTitle: string;
  verifySuccessPaymentDialogText: string;
  

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
  },
  translations: Translation;
  baseDomain: string;
}
  
export interface ProtonCheckOutState {
  
  appState?:string;
  isRunning:boolean,
  session?:LinkSession
  tx?:TransactResult,
  order?:any
  
}