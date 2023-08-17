
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
