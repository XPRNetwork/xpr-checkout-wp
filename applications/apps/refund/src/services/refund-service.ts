import { WPResponse } from 'xprcheckout';
import { Refund } from '../global';

export async function pushRefund(baseDomain:string,paymentKey:string,adminNonce:string):Promise<Refund> {
  
  const bodyData = {
    paymentKey
  };
  const fetchResult =  await fetch(`${baseDomain}/wp-json/xprcheckout/v2/admin/refund?_wpnonce=${adminNonce}`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
      
    
  })
    .then((res) => res.json() as Promise<WPResponse<Refund>>)
    return fetchResult.body_response

}