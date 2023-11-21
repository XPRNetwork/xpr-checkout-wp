import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from '../../commons/constants/endpoints';
import { JsonRpc } from "@proton/light-api"
import type { UserBalance } from '../type';


export async function getUserBalances(actor: string, isTestnet: boolean):Promise<UserBalance[]> {
  
  const rpc = new JsonRpc("proton")
  const userBalance = await rpc.get_balances(actor);
  if (userBalance && userBalance.balances) return userBalance.balances;
  return []
  

}