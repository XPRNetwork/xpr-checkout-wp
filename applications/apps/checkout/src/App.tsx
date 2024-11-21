import "./App.css";
import React from 'react'
import {XPRNProvider} from "xprnkit";
import { AppHeader } from "./components/app-header";
import { Stepper } from "./components/stepper";
import { ViewFlow } from "./components/views/view-flow";
import { CheckoutProvider } from "./providers/checkout-provider";
import { AppFooter } from "./components/app-footer";

const pluginConfig = window.pluginConfig;

function App() {
  return (
    <XPRNProvider
      config={{
        apiMode: pluginConfig.network,
        chainId: pluginConfig.chainId,
        endpoints: pluginConfig.endpoints.split(','),
        dAppName: "XPRCheckout",
        requesterAccount: "xprcheckout",
      }}
    >
      <main
        className=" flex flex-col px-4 gap-1 min-h-screen container max-w-[960px]"
      >
        
        <CheckoutProvider config={pluginConfig}>
          <AppHeader></AppHeader>
        <div className="grid grid-rows-[min-content,1fr] gap-1">
          <Stepper></Stepper>
          <ViewFlow></ViewFlow>
          </div>
        <AppFooter></AppFooter>
        </CheckoutProvider>
      </main>
    </XPRNProvider>
  );
}

export default App;
