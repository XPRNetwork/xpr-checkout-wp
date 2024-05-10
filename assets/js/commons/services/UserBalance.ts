import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from '../../commons/constants/endpoints';
import { JsonRpc } from "@proton/light-api"
import type { UserBalance } from '../type';


export async function getUserBalances(actor: string, isTestnet: boolean): Promise<UserBalance[]> {
  
  const TN_EP = TESTNET_ENDPOINTS[0]
  const MN_EP = MAINNET_ENDPOINTS[0]
  
  const rpc = new JsonRpc(isTestnet ? "protontest" : "proton")
  const userBalance = await rpc.get_balances(actor);
  if (userBalance && userBalance.balances) return userBalance.balances;
  return []
  

}