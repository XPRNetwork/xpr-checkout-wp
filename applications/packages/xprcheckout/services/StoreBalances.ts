import {JsonRpc} from '@proton/js'
import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from '../constants/endpoints';
export async function getStoreBalance(store:string,isTestnet:boolean) {
  
  const rpc = new JsonRpc(
    isTestnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS
  )

  try {
    const balanceQuey = await rpc.get_table_rows({
      code: 'wookey',
      scope: store,
      table:'balances'
    })
    return balanceQuey.rows;
  }catch(e){console.log(e)}

  return []
  

}