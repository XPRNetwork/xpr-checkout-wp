import { LinkSession, TransactResult } from "@proton/web-sdk";
import { BaseConfig } from "../commons/type";

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



export interface CartSession {
  paymentKey:string;
  cartTotal: string;
}

interface ConfigWithCart extends BaseConfig {
  cartSession: CartSession
}

  
export interface ProtonCheckOutState {
  appState?:string;
  isRunning:boolean,
  session?:LinkSession
  tx?:TransactResult,
  order?:any
}