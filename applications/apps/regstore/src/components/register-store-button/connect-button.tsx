import {useXPRN} from "xprnkit";
import {useCallback} from "react";
import { useRegstore } from "../../provider/regstore-provider";

type ConnectButtonProps = React.HTMLAttributes<HTMLDivElement>;

export const ConnectButton = (props: ConnectButtonProps) => {
  const {connect} = useXPRN();
  const {verifyChainStore,storeWallets,setStoreWallets,activeNetwork} = useRegstore()

  const onConnect = useCallback((e: React.MouseEvent) => {
    e.preventDefault();
    connect((session) => {
      verifyChainStore(session.auth.actor.toString()).then(res => {
        console.log('after connect',res)
        if (res) {
          if (!storeWallets || !activeNetwork) return;
          const mutatedWallet = {...storeWallets};
          mutatedWallet[activeNetwork].store = session.auth.actor.toString();
          mutatedWallet[activeNetwork].verified = res;
          setStoreWallets(mutatedWallet)
        }
      })
    });
  }, [connect,verifyChainStore,setStoreWallets,storeWallets,activeNetwork]);

    return (
      <button
        className="p-2 bg-black rounded-md grid grid-cols-[1fr,min-content] items-center"
        
        onClick={e => onConnect(e)}
      >
        {props.children}
        <svg className=" fill-white w-8 h-8" viewBox="0 0 61 37">
          <path d="M17.6468 0C19.3753 0 20.5959 1.69349 20.0493 3.33337L14.8767 18.8512C14.4482 20.1366 15.1429 21.526 16.4284 21.9545C17.7138 22.383 19.1032 21.6883 19.5317 20.4028L25.7554 1.73167C26.1001 0.697533 27.0679 0 28.1579 0H34.1082C35.1983 0 36.166 0.697532 36.5108 1.73167L42.7345 20.4028C43.163 21.6883 44.5524 22.383 45.8378 21.9545C47.1232 21.526 47.8179 20.1366 47.3894 18.8512L42.2168 3.33337C41.6702 1.69349 42.8908 0 44.6194 0H54.1726C55.5372 0 56.7481 0.87435 57.1775 2.16956L60.3437 11.7209C60.8974 13.3914 60.7826 15.2114 60.0233 16.799L51.4488 34.7277C51.028 35.6075 50.1394 36.1675 49.1641 36.1675H40.3194C39.2293 36.1675 38.2615 35.47 37.9168 34.4358L32.8333 16.3361L32.8287 16.3222C32.8264 16.3152 32.824 16.3083 32.8217 16.3014C32.8174 16.2888 32.813 16.2763 32.8085 16.2638C32.6773 15.8987 32.4678 15.5832 32.2053 15.33C31.9842 15.116 31.7197 14.9409 31.4193 14.8198C31.1304 14.7029 30.8185 14.6404 30.4999 14.6405C29.8607 14.6404 29.2485 14.8921 28.7946 15.33C28.5321 15.5832 28.3226 15.8987 28.1914 16.2638C28.1828 16.2877 28.1745 16.3119 28.1666 16.3361L23.0831 34.4358C22.7383 35.47 21.7706 36.1675 20.6805 36.1675H11.8359C10.8606 36.1675 9.97203 35.6075 9.55125 34.7277L0.9767 16.799C0.217399 15.2114 0.102574 13.3914 0.656321 11.7209L3.82252 2.16956C4.25187 0.87435 5.46285 0 6.82737 0H17.6468Z"></path>
          <path d="M28.9933 25.0113C29.1997 24.3506 29.8117 23.9006 30.504 23.9006H30.7092C31.4015 23.9006 32.0135 24.3506 32.22 25.0113L32.8135 26.9107C33.132 27.93 32.3706 28.9657 31.3027 28.9657H29.9105C28.8426 28.9657 28.0812 27.93 28.3997 26.9107L28.9933 25.0113Z"></path>
        </svg>
      </button>
    );  
};
