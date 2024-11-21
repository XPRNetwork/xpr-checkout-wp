import type { OraclePrice, WPResponse } from "../interfaces";

export async function getTokensOracle(baseDomain:string,currency:string):Promise<OraclePrice[]> {
  const bodyData = {
    currency:currency
  };
  const fetchResult = await fetch(`${baseDomain}/wp-json/xprcheckout/v2/prices-oracle`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
      
    
  })
    .then((res) => res.json() as Promise<WPResponse<OraclePrice[]>> )
    
  return fetchResult.body_response;
  

}