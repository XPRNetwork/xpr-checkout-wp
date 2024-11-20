import {useXPRN} from "xprnkit";


type DisconnectButtonProps = React.HTMLAttributes<HTMLDivElement>;

export const DisconnectButton = (props: DisconnectButtonProps) => {
  const {disconnect} = useXPRN();
  

    return (
      <button
        className="py-2 px-4 bg-black rounded-md grid grid-cols-[1fr,min-content] items-center font-bold text-white"
        onClick={e => disconnect()}
      >
        Logout
        
      </button>
    );  
};
