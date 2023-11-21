
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
  testnet: boolean;
  network: string;
  allowedTokens: string; 
  wooCurrency: string;
  baseDomain: string;
  translations: any;
}

interface ConfigWithOrder extends BaseConfig {
  transactionId: string;
  paymentKey: string;
  orderTotal: number;
}




