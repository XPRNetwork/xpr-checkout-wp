import React from "react";
import "./App.css";
import {XPRNProvider} from "xprnkit";
import "xprnkit/build/global.css";

import {RegStoreConfig} from "./global";
import {RegStoreProvider} from "./provider/regstore-provider";
import { RegStoreFlowSteps } from "./components/regstore-steps";


const pluginConfig: RegStoreConfig = window.pluginConfig;
console.log(pluginConfig);
function App() {
  
  return (
    <div className="App">
      <XPRNProvider
        config={{
          apiMode: pluginConfig.gatewayNetwork,
          chainId: pluginConfig.chainId,
          endpoints: pluginConfig.endpoints.split(","),
          dAppName: "XPRCheckout register store",
          requesterAccount: "xprcheckout",
        }}
      >
        <RegStoreProvider config={pluginConfig}>
          <RegStoreFlowSteps></RegStoreFlowSteps>
        </RegStoreProvider>
      </XPRNProvider>
    </div>
  );
}

export default App;
