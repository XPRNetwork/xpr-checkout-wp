import axios, { AxiosRequestConfig } from 'axios'

import type{ WPResponse,CartSession } from '../interfaces/type';
export function getCart(baseDomain:string) {
  
  const headers = {
    'Content-Type': 'application/json',
    
  };
  

  let config:AxiosRequestConfig = {
    method: 'get',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/xprcheckout/v1/cart`,
    withCredentials:true,
    headers
    
  };

  return axios<WPResponse<CartSession>>(config)

}