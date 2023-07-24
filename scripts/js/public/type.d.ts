export interface TokenRate{
  contract: string
  decimals: number
  symbol: string
  id: string
  quote: {
    price_usd:number
  }
}