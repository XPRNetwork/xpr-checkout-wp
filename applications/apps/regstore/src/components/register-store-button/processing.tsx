import {RegStoreConfig} from "../../global";
import { useRegstore } from "../../provider/regstore-provider";
import { StoreNameField } from "../store-name-field/store-name-field";


type PropsType = {
  config?: RegStoreConfig;
};

export const Processing = (props: PropsType) => {
 
  const {verificationState} = useRegstore()
  if (verificationState === 'verifying') {
    return (
      <div
        className="p-2 bg-gray-600 rounded-md inline-grid grid-cols-[1fr,min-content] items-center"
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
          className="lucide lucide-refresh-cw w-6 h-6 animate-spin stroke-white"
        >
          <path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8" />
          <path d="M21 3v5h-5" />
          <path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16" />
          <path d="M8 16H3v5" />
        </svg>
      </div>
    );  
  }
  return <></>
  
};
