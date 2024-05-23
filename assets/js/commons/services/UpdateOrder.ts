import axios, { AxiosRequestConfig } from 'axios'

import type{ WPResponse,Order } from '../type';

export function updateOrder(baseDomain:string,paymentKey:string,symbol:string,payer:string) {
  
  const headers = {
    'Content-Type': 'application/json',
    
  };
  
  let config:AxiosRequestConfig = {
    method: 'post',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/xprcheckout/v1/update-order`,
    data: {
      paymentKey: paymentKey,
      symbol: symbol,
      payer:payer
    },
    withCredentials:true,
    headers
    
  };

  return axios<WPResponse<Order>>(config)

}