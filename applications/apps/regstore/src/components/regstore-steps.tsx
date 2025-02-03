import React,{ useMemo } from "react"
import { RegStoreSteps, useRegstore } from "../provider/regstore-provider"
import { MessageHint } from "./message-hint/message-hint"
import { ConnectButton } from "./register-store-button/connect-button"
import { Processing } from "./register-store-button/processing"
import { RegisterStoreButton } from "./register-store-button/register-store-button"
import { UnregisterStoreButton } from "./register-store-button/unregister-store-button"
import { StoreNameField } from "./store-name-field/store-name-field"
import { useXPRN } from "xprnkit"
import { DisconnectButton } from "./register-store-button/disconnect-button"

type RegStoreStepsType = React.HTMLAttributes<HTMLDivElement>
export const RegStoreFlowSteps = (props: RegStoreStepsType) => { 

  const { step, storeWallets, activeNetwork } = useRegstore();
  const {session} = useXPRN()
  

  
  const connectLabel = useMemo(() => {

    if (!storeWallets) return;
    if (!activeNetwork) return;
    if (!storeWallets[activeNetwork].store) return `Connect a ${activeNetwork} account`;
    return `Connect ${storeWallets[activeNetwork].store} account`;

  },[storeWallets, activeNetwork])
 
  const registerLabel = useMemo(() => {

    if (!storeWallets) return;
    if (!activeNetwork) return;
    if (!session) return;
    return `Register @${session.auth.actor.toString()} on ${activeNetwork}`;

  },[storeWallets, activeNetwork,session])
  
  const unregisterLabel = useMemo(() => {

    if (!storeWallets) return;
    if (!activeNetwork) return;
    if (!storeWallets[activeNetwork].store) return;
    return `Unegister @${storeWallets[activeNetwork].store} on ${activeNetwork}`;

  },[storeWallets, activeNetwork])
  
  const savingLabel = useMemo(() => {

    if (!storeWallets) return;
    if (!activeNetwork) return;
    if (!session) return;
    return `Saving config @${session.auth.actor.toString()} for ${activeNetwork}`;

  },[storeWallets, activeNetwork,session])
  
  const verifyLabel = useMemo(() => {

    if (!storeWallets) return;
    if (!activeNetwork) return;
    if (!session) return;
    return `Checking @${session.auth.actor.toString()} with ${activeNetwork} contract`;

  },[storeWallets, activeNetwork,session])
  
  return (<><div >
    {
      step === RegStoreSteps.VERIFYING &&
      <Processing>
        <StoreNameField>
            {verifyLabel}  
        </StoreNameField>
      </Processing>
    }
    {
      step === RegStoreSteps.SAVING &&
      <Processing>
        <StoreNameField >
            {savingLabel}  
        </StoreNameField>
      </Processing>
    }
    {
      step === RegStoreSteps.REGISTER &&
      <div className="flex gap-2">

      <RegisterStoreButton>
        <StoreNameField>
          {registerLabel}
        </StoreNameField>
      </RegisterStoreButton>
      <DisconnectButton></DisconnectButton>
      </div>
    }
    {
      step === RegStoreSteps.UNREGISTER &&
      <div className="flex gap-2">

      <UnregisterStoreButton>
        <StoreNameField>
          {unregisterLabel}
        </StoreNameField>
      </UnregisterStoreButton>
      <DisconnectButton></DisconnectButton>
      </div>
    }
    {/* {step === RegStoreSteps.VERIFYING && <ClearStoreButton />} */}
    {
      step === RegStoreSteps.CONNECT &&
      <ConnectButton>
          <StoreNameField>
            { connectLabel}
          </StoreNameField>
      </ConnectButton>
    }
  </div>
    <MessageHint></MessageHint></>)

}