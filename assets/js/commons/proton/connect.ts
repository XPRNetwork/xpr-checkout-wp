import { LINK_STORAGE_PREFIX, MAINNET_CHAIN_ID, TESTNET_CHAIN_ID } from "../constants/chain";
import { MAINNET_ENDPOINTS, TESTNET_ENDPOINTS } from "../constants/endpoints";
import ProtonWeb, { type LinkSession, type TransactResult } from '@proton/web-sdk';

export async function webauthConnect(requestAccount:string,appName:string,testnet:boolean,restoreSession = false):Promise<LinkSession | undefined | null> {
    
  try {
    const { session, link } = await ProtonWeb({
      linkOptions: {
        chainId: testnet ? TESTNET_CHAIN_ID : MAINNET_CHAIN_ID,
        endpoints: testnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS,
        restoreSession: restoreSession,
        storagePrefix: LINK_STORAGE_PREFIX
      },
      transportOptions: {
        requestAccount: requestAccount,
      },
      selectorOptions: {
        appName: appName,
      }
    })
    return session;
  } catch (e) { 

    return null;

  }

}