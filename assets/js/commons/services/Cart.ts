import axios, { AxiosRequestConfig } from 'axios'
export function getCart(baseDomain:string) {
  
  let config:AxiosRequestConfig = {
    method: 'get',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/wookey/v1/cart`,
    withCredentials:true,
    headers: { 
      'Content-Type': 'application/json', 
    },
    
  };

  return axios(config)

}