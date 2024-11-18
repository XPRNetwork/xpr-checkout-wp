import React from "react";
import "./App.css";
import {XPRNProvider} from "xprnkit";
import "xprnkit/build/global.css";

import {RegisterStoreButton} from "./components/register-store-button/register-store-button";
import {RegStoreConfig} from "./global";
import {ConnectButton} from "./components/register-store-button/connect-button";
import {RegStoreProvider} from "./provider/regstore-provider";
import {Processing} from "./components/register-store-button/processing";
import {UnregisterStoreButton} from "./components/register-store-button/unregister-store-button";
import { MessageHint } from "./components/message-hint/message-hint";
import { ClearStoreButton } from "./components/register-store-button/clear-store-button";

const pluginConfig: RegStoreConfig = window.pluginConfig;

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
          <div >
            <Processing />
            <RegisterStoreButton />
            <UnregisterStoreButton />
            <ClearStoreButton />
            <ConnectButton />
            
            
          </div>
            <MessageHint></MessageHint>
        </RegStoreProvider>
      </XPRNProvider>
    </div>
  );
}

export default App;
