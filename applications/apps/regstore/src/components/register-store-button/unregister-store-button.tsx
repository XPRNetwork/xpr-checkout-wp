import {useXPRN} from "xprnkit";
import {useCallback} from "react";
import {xprcheckout} from "../../interfaces/xprcheckout";
import {useRegstore} from "../../provider/regstore-provider";
import { StoreNameField } from "../store-name-field/store-name-field";

type PropsType = {};

export const UnregisterStoreButton = (props: PropsType) => {
  const {session} = useXPRN();
  const {verificationState,updateField} = useRegstore();

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
        updateField("");
      }).catch(() => {
        updateField("");
      })
    },
    [session,updateField]
  );

  if (verificationState === "verified" && session) {
    return (
      <button
        className="p-2 bg-brand rounded-md grid grid-cols-[1fr,min-content] items-center"
        onClick={e => unregisterStore(e)}
      >
        <StoreNameField ></StoreNameField>
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
  return <></>;
};
