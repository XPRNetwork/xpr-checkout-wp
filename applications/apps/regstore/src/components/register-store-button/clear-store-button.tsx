import {useXPRN} from "xprnkit";
import {useCallback} from "react";
import {useRegstore} from "../../provider/regstore-provider";
import {StoreNameField} from "../store-name-field/store-name-field";

type PropsType = {store?:string};

export const ClearStoreButton = (props: PropsType) => {
  const { session, disconnect } = useXPRN();
  const {updateField} = useRegstore()
  
  
  const onClear = useCallback(() => {
    
    updateField('');
    disconnect();

  },[disconnect])


  
    return (
      <button
        className="p-2 bg-red-500 rounded-md grid grid-cols-[1fr,min-content]"
        onClick={e => onClear()}
      >
        <StoreNameField></StoreNameField>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="lucide lucide-eraser w-6 h-6 stroke-white"
        >
          <path d="m7 21-4.3-4.3c-1-1-1-2.5 0-3.4l9.6-9.6c1-1 2.5-1 3.4 0l5.6 5.6c1 1 1 2.5 0 3.4L13 21" />
          <path d="M22 21H7" />
          <path d="m5 11 9 9" />
        </svg>
      </button>
    );
  
  
};
