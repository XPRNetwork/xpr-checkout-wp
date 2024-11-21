import { RegStoreConfig } from "../global";


export function  isValidStoreAccountConfig (storeAccountConfig?:RegStoreConfig):boolean { 

  if (!storeAccountConfig) return false;
  if (!storeAccountConfig.store || storeAccountConfig.store === "") return false;
  return true


}