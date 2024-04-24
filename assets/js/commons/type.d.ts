
export interface TokenRate{
  contract: string
  decimals: number
  symbol: string
  id: string
  logo?: string;
  quote: {
    price_usd: number
  };
  availableUserBalance: number;
  enable: boolean;
  pair_base: string;
}

export interface UserBalance {
    currency: string
    contract: string
    decimals: string
    amount: string
}


export interface WPResponse<T> {
  status: number;
  body_response: T;
}

export interface PaymentVerifyResponse {
  paymentKey: string;
  transactionId: string;
  validated: boolean;  
}

interface BaseConfig {
  mainnetActor: string;
  testnetActor: string;
  appName: string;
  testnet: string;
  network: string;
  allowedTokens: string; 
  wooCurrency: string;
  baseDomain: string;
  translations: any;
  wooCheckoutUrl: string;
  nonce: string;
  
}

interface ConfigWithOrder extends BaseConfig {
  transactionId: string;
  paymentKey: string;
  orderTotal: number;
}

import {LinkSession, TransactResult} from "@proton/web-sdk";


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
  paymentKey: string;
  cartTotal: string;
}

export interface Order {
  paymentKey: string;
  transactionId: string;
  payer: string;
  paymentVerified: boolean;
  currency: string;
  fillRatio: number;
  status: string;
  total: number;
  token: string;
  orderKey: string;
  orderId: number;
  cancelRedirect: string;
  continueRedirect:string
}

interface ConfigWithCart extends BaseConfig {
  cartSession: CartSession;
}

interface ConfigWithOrder extends BaseConfig {
  order: Order;
}

export interface ProtonCheckOutState {
  appState?: number;
  isRunning: boolean;
  session?: LinkSession;
  tx?: TransactResult;
  order?: any;
}





