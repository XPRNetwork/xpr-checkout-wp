import {useXPRN} from "xprnkit";
import {useRegstore} from "../../provider/regstore-provider";
import React,{useMemo} from "react";

export const MessageHint = () => {
  const {session} = useXPRN();
  const {activeNetwork, activeNetworkConfig} = useRegstore();

  const newAccountLink = useMemo(() => {
    if (!activeNetworkConfig) return "https://testnet.webauth.com/";
    if (activeNetwork === "mainnet") return "https://webauth.com/";
    return "https://testnet.webauth.com/";
  }, [activeNetworkConfig, activeNetwork]);

  if (session) return <></>;

  return (
    <>
      <p>
        Need a{" "}
        <a
          target="_blank"
          rel="noreferrer"
          className="underline text-brand"
          href={newAccountLink}
        >
          new { activeNetwork } account
        </a>
        ?
      </p>
    </>
  );
};
