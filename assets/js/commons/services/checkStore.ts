import {JsonRpc} from '@proton/js'
import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from '../constants/endpoints';


export async function isStoreRegistered(storeAccount:string,isTestnet:boolean) {
  
  if (storeAccount == '') return false
  const rpc = new JsonRpc(
    isTestnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS
  )
  
  const balanceQuey = await rpc.get_table_rows({
    code: 'wookey',
    scope: 'wookey',
    table: 'stores',
    lower_bound: storeAccount,
    upper_bound:storeAccount
  })
console.log(balanceQuey.rows)
  return !!balanceQuey.rows[0];

  

}