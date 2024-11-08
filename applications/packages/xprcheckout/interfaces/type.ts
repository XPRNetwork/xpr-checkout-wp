
export interface TokenRate{
  contract: string
  decimals: number
  symbol: string
  id: string
  logo?: string;
  quote: {
    price_usd: number
  };
  
  pair_base: string;
}

export interface TokenConversion {
  symbol: string;
  amount: string;
  logo: string;
  contract: string;
}

export interface UserBalanceConversion extends TokenConversion {
  balance: number,
  enabled: boolean;
}

export interface OrderPayment {
  usd_amount: number;
  base_currency: string;
  base_amount: string;
  converted: TokenConversion[];
  status: string;
  verified: boolean;
}

export interface PaymentTokenLink {
  contract: string
  logo?: string;
  pair_base: string;
  token_amount: string;
  fiat_amount: string;
  availableUserBalance: number;
  enable: boolean;
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

export interface PluginBaseConfig {
  store: string;
  chainId: string;
  endpoints: string;
  gatewayNetwork: 'testnet' | 'mainnet';
  baseDomain: string;
  
}

export interface OraclePrice {
  tokenSymbol: number,
  fiatRate: number,
  tokenContract: string,
  symbolName: string,
  precision: 6
}




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






