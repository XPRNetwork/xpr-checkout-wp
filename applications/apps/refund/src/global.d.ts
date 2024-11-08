// globals.d.ts

import { PluginBaseConfig } from "xprcheckout";

export interface Refund {
  refunded:boolean
}

// Define the structure of params
export interface RefundConfig extends PluginBaseConfig {
  requestedPaymentKey: string;
  amountToRefund: string;
  accountToRefund: string;
  orderStatus: string;
  adminNonce: string;

  // Add other properties as needed
}

// Augment the global Window interface
declare global {
  interface Window {
    pluginConfig: RefundConfig;
  }
}

export {}; // This ensures the file is treated as a module
