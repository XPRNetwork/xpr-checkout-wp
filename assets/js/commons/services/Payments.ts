import {JsonRpc} from '@proton/js'
import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from '../constants/endpoints';
import axios from 'axios';
import { toEOSIOSha256 } from '../utils/sha256';
export async function getPayments(baseDomain:string,store:string,isTestnet:boolean) {
  


  let config = {
    method: 'post',
    maxBodyLength: Infinity,
    url: `${baseDomain}/wp-json/wookey/v1/admin/payments`,
    headers: { 
      'Content-Type': 'application/json', 
    },
    data: {
      actor: store,
      network:isTestnet ? "testnet" : "mainnet"
    }
    
  };

  return axios(config)

  

}

export async function getPayment(paymentKey:string,isTestnet:boolean) {
  
  const rpc = new JsonRpc(
    isTestnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS
  )
  
  const balanceQuey = await rpc.get_table_rows({
    code: 'wookey',
    scope: 'wookey',
    table: 'payments',
    index_position: 2,
    key_type: 'sha256',
    lower_bound: toEOSIOSha256(paymentKey),
    upper_bound:toEOSIOSha256(paymentKey)
  })
  console.log('have payment?',balanceQuey.rows[0])
  return balanceQuey.rows[0] || null;

  

}