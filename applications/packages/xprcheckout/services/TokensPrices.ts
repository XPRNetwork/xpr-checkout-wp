import axios from 'axios'
import type { TokenRate, WPResponse } from '../interfaces';
export async function getTokensPrices(baseDomain:string):Promise<TokenRate[]> {
  
  const queryResult = await fetch(`${baseDomain}/wp-json/xprcheckout/v1/tokens-prices`, {
    method:'post',
    headers: {
      'Content-Type': 'application/json',
    },
  }).then((res) => res.json() as Promise<WPResponse<TokenRate[]>>);
  return queryResult.body_response;
}