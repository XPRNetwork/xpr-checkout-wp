
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


export interface VerifyPaymentResponse {
  status: number;
  body_response: {
    paymentKey: string;
    transactionId: string;
    validated: boolean;  
  }
}

interface BaseConfig {
  mainnetActor: string;
  testnetActor: string;
  testnet: boolean;
  network: string;
  allowedTokens: string[]; 
  wooCurrency: string;
  baseDomain: string;
}

interface ConfigWithOrder extends BaseConfig {
  transactionId: string;
  paymentKey: string;
  orderTotal: number;
}

interface ConfigWithCart extends BaseConfig {
  cartTotal: number;
  paymentKey: string;
}


