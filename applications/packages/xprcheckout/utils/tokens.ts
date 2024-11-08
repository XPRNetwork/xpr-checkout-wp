import type {TokenRate, UserBalance } from "../interfaces/type";
import { toPrecision } from "./price";

export function getUserBalanceForToken(token: string, userBalances?: UserBalance[]) { 
  if (!userBalances) return 0
  const foundToken = userBalances.find((balance) => balance.currency == token)
  if (foundToken) return parseFloat(foundToken.amount)
  return 0
}

export function convertFiatPriceToToken (fiatAmount:number,symbol:string,allowedTokenRates:TokenRate[]){
  const rate = getTokenRateBySymbol(symbol, allowedTokenRates);
  if (rate){
    console.log(fiatAmount,rate.quote.price_usd,rate.decimals)
    return parseFloat(toPrecision(fiatAmount/rate.quote.price_usd,rate.decimals));
  }
  return 0
}

export function getTokenRateBySymbol (symbol:string,allowedTokenRates:TokenRate[]):TokenRate | undefined{
  return allowedTokenRates.find((token)=>token.symbol == symbol);
}

export function orderTokensRate (a:TokenRate,b:TokenRate) {

  console.log('order',a,b)
  return 0

}