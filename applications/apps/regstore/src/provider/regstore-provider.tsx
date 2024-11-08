import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
} from "react";

import {RegStoreConfig} from "../global";
import {verifyStore} from "../services/verify-store";
import { wait } from "../utils/wait";

export type RegStoreProviderTypes = {
  verifyChainStore: (storeName: string) => Promise<boolean>;
  verificationState?: StoreNameStatus;
  verificationStatus?: boolean;
  storeName?: string,
  config?:RegStoreConfig,
  updateField:(storeName:string)=>void
};

const regstoreContext = createContext<RegStoreProviderTypes>({
  verifyChainStore: (storeName: string):Promise<boolean> => { return new Promise<boolean>(()=>true) },
  verificationState: 'empty',
  verificationStatus: false,
  storeName: '',
  updateField:(storeName:string)=>{}
});

type StoreNameStatus =
  | "empty"
  | "verifying"
  | "verified"
  | "unverified"
  | "unsaved"
  | "validate";
type RegStoreProviderProps = React.HTMLAttributes<HTMLDivElement> & {
  config: RegStoreConfig;
};
export const RegStoreProvider: React.FunctionComponent<
  RegStoreProviderProps
> = ({children, config}) => {
  const [verificationState, setVerificationState] = useState<StoreNameStatus>();
  const [verificationStatus, setVerificationStatus] = useState<boolean>();
  const [storeName, setStoreName] = useState<string>();

  const updateField = useCallback(
    (session: string) => {
      const walletInput: HTMLInputElement = document.querySelector(
        config.walletInputSelector
      ) as HTMLInputElement;
      if (walletInput) walletInput.value = session;
      if (session === "") setVerificationState('empty');
      setStoreName(session)
    },
    [config]
  );

  const verifyChainStore = useCallback(
    (storeName: string):Promise<boolean> => {
      setVerificationState("verifying");
      return wait(5000).then(() => {
        return verifyStore(storeName, config.endpoints.split(",")).then(async(res) => {
          if (res) {
            setVerificationState("verified");
          } else {
            setVerificationState("unverified");
          }
          updateField(storeName)
          setStoreName(storeName);
          return res;
        });
      })
      
    },
    [config,updateField]
  );

  useEffect(() => {
    if (config.store === "") { 
      console.log('no store yet')
      setVerificationState('empty');
    }
    if (!config) return;
    if (!config.store) return;
    
    verifyChainStore(config.store);
    setStoreName(config.store);
  }, [config,verifyChainStore]);

  return (
    <regstoreContext.Provider
      value={{
        verifyChainStore,
        verificationState,
        verificationStatus,
        storeName,
        updateField,
        config
      }}
    >
      {children}
    </regstoreContext.Provider>
  );
};

export function useRegstore() {
  return useContext(regstoreContext);
}
