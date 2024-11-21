import type { WPResponse,OrderPayment } from "xprcheckout";
import { StoreWalletConfig } from "../global";

export async function saveWalletConfig(baseDomain:string,wallets: StoreWalletConfig,adminNonce:string) {
  
  console.log('sacve wallet config')
  const bodyData = {
    wallets
  };
  const fetchResult = await fetch(`${baseDomain}/wp-json/xprcheckout/v2/admin/save-wallet-config?_wpnonce=${adminNonce}`,{
    method: 'post',
    body: JSON.stringify(bodyData),
    headers:{ 
      'Content-Type': 'application/json', 
    },
      
    
  })
    .then((res) => res.json() as Promise<WPResponse<OrderPayment>> )
    
  return fetchResult.body_response;
  

}