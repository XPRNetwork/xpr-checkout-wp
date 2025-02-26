import {useXPRN} from "xprnkit";
import React,{useCallback} from "react";
import {xprcheckout} from "../../interfaces/xprcheckout";
import {useRegstore} from "../../provider/regstore-provider";



type RegisterStoreButtonProps = React.HTMLAttributes<HTMLDivElement>;

export const RegisterStoreButton = (props: RegisterStoreButtonProps) => {
  const {session} = useXPRN();
  const {verifyChainStore,activeNetwork,setStoreWallets,storeWallets,updateWalletConfig} = useRegstore();

  const registerStore = useCallback(
    (e: React.MouseEvent) => {
      e.preventDefault();
      if (!session || !activeNetwork ) return;
      const action = xprcheckout.store_reg(
        [
          {
            actor: session.auth.actor.toString(),
            permission: session.auth.permission.toString(),
          },
        ],
        {storeAccount: session.auth.actor.toString()}
      );
      session.transact({ actions: [action] }, { broadcast: true }).then(() => {
        verifyChainStore(session.auth.actor.toString()).then((res) => {
          if (res) {
            if (!activeNetwork || !storeWallets) return 
            const mutatedStoreWallets = {...storeWallets} ;
            mutatedStoreWallets[activeNetwork].store = session.auth.actor.toString();
            mutatedStoreWallets[activeNetwork].verified = res
            setStoreWallets(mutatedStoreWallets);
           
          }
        }).then(() => {
          updateWalletConfig()
        })
       
      })
    },
    [session, activeNetwork, verifyChainStore, storeWallets, setStoreWallets, updateWalletConfig]
  );

  
    return (
      <button
        className="p-2 bg-brand rounded-md grid grid-cols-[1fr,min-content] items-center"
        onClick={e => registerStore(e)}
      >
       {props.children}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="lucide lucide-plus w-6 h-6 stroke-white"
        >
          <path d="M2 21a8 8 0 0 1 13.292-6" />
          <circle cx="10" cy="8" r="5" />
          <path d="M19 16v6" />
          <path d="M22 19h-6" />
        </svg>
      </button>
    );
  }

