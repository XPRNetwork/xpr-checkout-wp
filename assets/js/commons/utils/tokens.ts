import type {UserBalance } from "../type";

export function getUserBalanceForToken(token:string,userBalances:UserBalance[]) { 

  const foundToken = userBalances.find((balance) => balance.currency == token)
  if (foundToken) return foundToken
  return null


}