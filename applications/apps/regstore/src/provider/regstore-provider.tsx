import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useRef,
  useState,
} from "react";

import {RegStoreConfig, StoreWalletConfig, WalletConfig} from "../global";
import {verifyStore} from "../services/verify-store";
import {wait} from "../utils/wait";
import {Network, NetworkConfig} from "xprcheckout";
import {useXPRN} from "xprnkit";
import {saveWalletConfig} from "../services/save-wallets-config";

export const DEFAULT_STORE_WALLETS: StoreWalletConfig = {
  testnet: {store: "", verified: false},
  mainnet: {store: "", verified: false},
};

export enum RegStoreSteps {
  CONNECT,
  REGISTER,
  UNREGISTER,
  VERIFYING,
  SAVING,
}
type StoreNameStatus =
  | "empty"
  | "verifying"
  | "verified"
  | "saving"
  | "saved"
  | "unverified";

type RegStoreProviderTypes = {
  step?: RegStoreSteps;
  verificationState?: StoreNameStatus;
  storeWallets?: StoreWalletConfig;
  activeNetwork?: Network;
  activeNetworkConfig?: NetworkConfig;
  activeStore?: WalletConfig;
  verifyChainStore: (storeName: string) => Promise<boolean>;
  updateField: (storeName: string) => void;
  setStoreWallets: (wallet: StoreWalletConfig) => void;
  updateWalletConfig: () => Promise<void>;
};

const regstoreContext = createContext<RegStoreProviderTypes>({
  verificationState: "empty",
  verifyChainStore: async () => true,
  setStoreWallets: () => {},
  updateField: (storeName: string) => {},
  updateWalletConfig: async () => {},
});

type RegStoreProviderProps = React.HTMLAttributes<HTMLDivElement> & {
  config: RegStoreConfig;
};

export const RegStoreProvider: React.FunctionComponent<
  RegStoreProviderProps
> = ({children, config}) => {
  const [verificationState, setVerificationState] =
    useState<StoreNameStatus>("empty");
  const [storeWallets, setStoreWallets] = useState<StoreWalletConfig>(
    DEFAULT_STORE_WALLETS
  );
  const [activeNetwork, setActiveNetwork] = useState<Network>(
    config.gatewayNetwork
  );
  const [currentStep, setCurrentStep] = useState<RegStoreSteps>();

  const [activeNetworkConfig, setActiveNetworkConfig] = useState<
    NetworkConfig | undefined
  >(config.networks[config.gatewayNetwork]);
  const inited = useRef<boolean>(false);

  const {session, updateConfig, disconnect} = useXPRN();

  const updateField = useCallback(
    (session: string) => {
      const walletInput = document.querySelector(
        config.walletInputSelector
      ) as HTMLInputElement;
      if (walletInput) walletInput.value = session;
    },
    [config]
  );

  const verifyChainStore = useCallback(
    async (storeName: string): Promise<boolean> => {
      if (!activeNetworkConfig) return false;
      if (storeName === "" || storeName.length < 4) return false;
      setVerificationState("verifying");
      await wait(5000);
      const result = await verifyStore(
        storeName,
        activeNetworkConfig.endpoints.split(",")
      );
      const mutatedWallet = {...storeWallets};
      mutatedWallet[activeNetwork].verified = result;
      setVerificationState(result ? "verified" : "unverified");
      return result;
    },
    [activeNetwork, activeNetworkConfig, storeWallets]
  );

  const updateWalletConfig = useCallback(async () => {
    setVerificationState("saving");
    await saveWalletConfig(config.baseDomain, storeWallets, config.adminNonce);
    setVerificationState("saved");
  }, [config, storeWallets]);

  const activeStore = useMemo(() => {
    if (!storeWallets) return;
    if (!activeNetwork) return;
    return storeWallets[activeNetwork];
  }, [storeWallets, activeNetwork]);

  useEffect(() => {
    if (!config || !config.networks || !config.gatewayNetwork) return;
    setStoreWallets(config.wallets);
    inited.current = true;
  }, [config]);

  useEffect(() => {
    if (activeNetwork) {
      const activeConfig = config.networks[activeNetwork];
      setActiveNetworkConfig(activeConfig);
      updateConfig({
        endpoints: activeConfig.endpoints.split(","),
        chainId: activeConfig.chainId,
      });
    }
  }, [activeNetwork, config, updateConfig]);

  useEffect(() => {
    updateField(JSON.stringify(storeWallets));
  }, [storeWallets, updateField]);

  useEffect(() => {
    if (!storeWallets || !activeNetwork) return;
    const activeWallet = storeWallets[activeNetwork];
    if (activeWallet.verified) return;
    verifyChainStore(activeWallet.store);
  }, [storeWallets, activeNetwork, verifyChainStore]);

  useEffect(() => {
    if (config.networkFieldSelector) {
      const selectorDomElement = document.querySelector(
        config.networkFieldSelector
      );
      if (!selectorDomElement) return;
      const element = selectorDomElement as HTMLInputElement;
      const handleNetworkChange = (e: Event) => {
        const target = e.target as HTMLInputElement;
        setActiveNetwork(target.value as Network);
        console.log(target.value, "When select change");
        disconnect();
      };

      element.addEventListener("change", handleNetworkChange);

      return () => {
        element.removeEventListener("change", handleNetworkChange);
      };
    }
  }, [config, disconnect]);

  useEffect(() => {
    if (!storeWallets || !activeNetwork) return;
    console.log(session,storeWallets);
    if (verificationState === "verifying") {
      setCurrentStep(RegStoreSteps.VERIFYING);
      return;
    }
    if (verificationState === "saving") {
      setCurrentStep(RegStoreSteps.SAVING);
      return;
    }
    if (!session) {
      setCurrentStep(RegStoreSteps.CONNECT);
      return;
    } else if (session && storeWallets[activeNetwork].verified) {
      setCurrentStep(RegStoreSteps.UNREGISTER);
      return;
    } else if (session && !storeWallets[activeNetwork].verified) {
      setCurrentStep(RegStoreSteps.REGISTER);
      return;
    }
  }, [session, activeNetwork, storeWallets, verificationState]);

  return (
    <regstoreContext.Provider
      value={{
        step: currentStep,
        verificationState,
        storeWallets,
        activeNetwork,
        activeNetworkConfig,
        activeStore,
        verifyChainStore,
        setStoreWallets,
        updateField,
        updateWalletConfig,
      }}
    >
      {children}
    </regstoreContext.Provider>
  );
};

export function useRegstore() {
  return useContext(regstoreContext);
}
