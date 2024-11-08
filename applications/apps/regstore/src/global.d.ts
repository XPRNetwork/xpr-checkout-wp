// globals.d.ts
import {} from 'xp'
import { PluginBaseConfig } from 'xprcheckout';
// Define the structure of params
export interface RegStoreConfig extends PluginBaseConfig  {
  
  store: string;
  walletInputSelector: string;
  networkSelectSelector: string;
  // Add other properties as needed
}

// Augment the global Window interface
declare global {
  interface Window {
    pluginConfig: RegStoreConfig;
  }
}

export {}; // This ensures the file is treated as a module
