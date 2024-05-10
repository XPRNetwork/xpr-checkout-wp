import axios, { AxiosRequestConfig } from 'axios'
import type{ PaymentVerifyResponse, WPResponse,Order } from './../type';

export function verifyPayment(baseDomain:string,paymentKey: string,actor:string,network:string="testnet") {
  
  
  let data = JSON.stringify({
    "paymentKey": paymentKey,
    "actor": actor,
    "network":network
  });

  let config:AxiosRequestConfig = {
    method: 'post',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/wookey/v1/verify-transaction`,
    headers: { 
      'Content-Type': 'application/json', 
    },
    data : data
  };

  return axios<WPResponse<Order>>(config)

}