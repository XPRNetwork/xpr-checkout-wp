import {useXPRN} from "xprnkit";
import React,{useCallback} from "react";
import {xprcheckout} from "../../interfaces/xprcheckout";
import {useRegstore} from "../../provider/regstore-provider";


type UnregisterStoreButtonProps = React.HTMLAttributes<HTMLDivElement>;

export const UnregisterStoreButton = (props: UnregisterStoreButtonProps) => {
  const {session} = useXPRN();
  const {setStoreWallets,storeWallets,activeNetwork,updateWalletConfig} = useRegstore();

  const unregisterStore = useCallback(
    (e: React.MouseEvent) => {
      e.preventDefault();
      if (!session) return;
      const action = xprcheckout.store_unreg(
        [
          {
            actor: session.auth.actor.toString(),
            permission: session.auth.permission.toString(),
          },
        ],
        {storeAccount: session.auth.actor.toString()}
      );
      session.transact({ actions: [action] }, { broadcast: true }).then(() => {
        console.log('after unreg',activeNetwork,storeWallets)
        if (!activeNetwork || !storeWallets) return;
        console.log('-> go')
        const mutatedStoreWallets = {...storeWallets} ;
        mutatedStoreWallets[activeNetwork].store = "";
        mutatedStoreWallets[activeNetwork].verified = false
        setStoreWallets(mutatedStoreWallets);
        
      }).then(() => {
          updateWalletConfig()
        
      }).catch(() => {
        
      })
    },
    [activeNetwork, session, setStoreWallets, storeWallets,updateWalletConfig]
  );

  
    return (
      <button
        className="p-2 bg-brand rounded-md grid grid-cols-[1fr,min-content] items-center"
        onClick={e => unregisterStore(e)}
      >
        {props.children}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"   
          className="lucide lucideMlus w-6 h-6 stroke-white"
        >
          <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
          <circle cx="9" cy="7" r="4" />
          <line x1="22" x2="16" y1="11" y2="11" />
        </svg>
      </button>
    );
  }

