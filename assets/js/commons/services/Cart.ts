import axios, { AxiosRequestConfig } from 'axios'

import type{ WPResponse } from '../type';
import type{ CartSession } from '../../public/public.type';
export function getCart(baseDomain:string) {
  
  const headers = {
    'Content-Type': 'application/json',
    
  };
  

  let config:AxiosRequestConfig = {
    method: 'get',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/wookey/v1/cart`,
    withCredentials:true,
    headers
    
  };

  return axios<WPResponse<CartSession>>(config)

}