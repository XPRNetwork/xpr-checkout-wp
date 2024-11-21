// globals.d.ts
import {} from 'xp'
import { Network, PluginBaseConfig } from 'xprcheckout';
// Define the structure of params
export interface RegStoreConfig extends PluginBaseConfig  {
  
  store: string;
  walletInputSelector: string;
  networkFieldSelector: string;
  adminNonce: string;
  wallets: StoreWalletConfig;
  // Add other properties as needed
}


export type WalletConfig = {store:string,verified:boolean}

export type StoreWalletConfig =  Record<Network, WalletConfig>;

// Augment the global Window interface
declare global {
  interface Window {
    pluginConfig: RegStoreConfig;
  }
}

export {}; // This ensures the file is treated as a module
