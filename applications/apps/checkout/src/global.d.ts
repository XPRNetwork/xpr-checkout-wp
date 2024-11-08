// globals.d.ts

import { PluginBaseConfig } from "xprcheckout";

// Define the structure of params
export interface CheckoutConfig extends PluginBaseConfig {
  orderTotal: string;
  network: 'testnet' | 'mainnet';
  allowedTokens: string;
  appName: string;
  baseDomain: string;
  requestedPaymentKey: string;
  store: string;
  wooCheckoutUrl: string;
  wooCurrency: string;
  wooThankYouUrl: string;

  // Add other properties as needed
}

// Augment the global Window interface
declare global {
  interface Window {
    pluginConfig: CheckoutConfig;
  }
}

export {}; // This ensures the file is treated as a module
