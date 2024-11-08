import {useXPRN} from "xprnkit";
import {useRegstore} from "../../provider/regstore-provider";
import {useMemo} from "react";

export const MessageHint = () => {
  const {session} = useXPRN();
  const {verificationState, config,storeName,verifyChainStore} = useRegstore();

  const newAccountLink = useMemo(() => {
    if (!config) return "https://testnet.webauth.com/";
    if (config.gatewayNetwork === "mainnet") return "https://webauth.com/";
    return "https://testnet.webauth.com/";
  }, [config]);

  if (!config) return <></>;
  return (
    <>
      {!session && verificationState === "unverified" && (
        <p className="text-red-500">Not registered, connect to fix</p>
      )}
      {session && verificationState === "unverified" && (
        <p className="text-red-500">Clear or <button className="underline" onClick={()=>verifyChainStore(storeName!)}>reverify</button></p>
      )}
      {!session && verificationState === "verified" && (
        <p>Connect to edit</p>
      )}
      {session && verificationState === "verified"  && (
        <>
          {storeName !== config.store && <p>Save config to apply change</p>}
        </>
      )}
      {!session && verificationState === "empty" && (
        <p>
          Need a{" "}
          <a
            target="_blank"
            rel="noreferrer"
            className="underline text-brand"
            href={newAccountLink}
          >
            new account
          </a>
          ?
        </p>
      )}
      {session && verificationState === "empty" && (
        <p>Register <span className="font-bold text-brand">@{session.auth.actor.toString() }</span> in stores.</p>
      )}
      {session && verificationState === "verified" && storeName === config.store && (
        <p>Unregister <span className="font-bold text-brand">@{session.auth.actor.toString() }</span> from stores.</p>
      )}
    </>
  );
};
