import axios, { type AxiosRequestConfig } from 'axios'

import type{ WPResponse,Order } from '../interfaces';

export async function updateOrder(baseDomain:string,paymentKey:string,symbol:string,payer:string):Promise<Order[]> {
  
  const bodyData = {
    paymentKey: paymentKey,
    symbol: symbol,
    payer: payer
  };
  const fetchResult =  await fetch(`${baseDomain}/wp-json/xprcheckout/v1/update-order`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
  })
    .then((res) => res.json() as Promise<WPResponse<Order[]>>)
    return fetchResult.body_response

}