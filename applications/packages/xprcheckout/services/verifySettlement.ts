import axios from 'axios'
import type{ PaymentVerifyResponse, WPResponse,Order } from '../interfaces/type';

export async function verifySettlement(baseDomain:string,paymentKey: string):Promise<any> {
  
  let bodyData ={
    "paymentKey": paymentKey
  };

  const fetchResult = await fetch(`${baseDomain}/wp-json/xprcheckout/v1/verify-settlement`, {
    method: 'post',
    body: JSON.stringify(bodyData)
  }).then((res) => res.json() as Promise<WPResponse<any>>);
  

  return fetchResult.body_response

}