import type { WPResponse,OrderPayment } from "../interfaces";

export async function getOrderPayment(baseDomain:string,paymentKey: string) {
  
  const bodyData = {
    paymentKey:paymentKey
  };
  const fetchResult = await fetch(`${baseDomain}/wp-json/xprcheckout/v2/order-payment`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
      
    
  })
    .then((res) => res.json() as Promise<WPResponse<OrderPayment>> )
    
  return fetchResult.body_response;
  

}