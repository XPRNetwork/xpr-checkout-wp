import axios from 'axios'
import type { WPResponse } from '../interfaces';
export async function getConvertedToUSD(baseDomain:string,storeCurrency:string,orderAmount:number):Promise<number> {
  
  

  const bodyData = {
    "storeCurrency": storeCurrency,
    "amount": orderAmount,
  };
  const fetchResult =  await fetch(`${baseDomain}/wp-json/xprcheckout/v1/price-rates`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
      
    
  })
    .then((res) => res.json() as Promise<WPResponse<number>>)
    return fetchResult.body_response

}