import React, {useEffect, useState} from "react";
import {toPrecision, useXPRN} from "xprnkit";
import {useCheckout} from "../../providers/checkout-provider";
import { getUserBalanceForToken, TokenConversion, UserBalanceConversion } from "xprcheckout";
import { Processing } from "../processing";
import { TokenSelectRenderItem } from "../token-select-render-items";

type CheckoutTokenSelectProps = React.HTMLAttributes<HTMLDivElement> & {};
export const CheckoutTokenSelect: React.FunctionComponent<
  CheckoutTokenSelectProps
> = () => {
  const {
    refreshTokensList,
    processPayment,
    orderPayment,
    userBalances,
    asyncStatus
  } = useCheckout();
  const { session } = useXPRN();
  const [tokensWithUserBalances,setTokensWithUserBalances] = useState<UserBalanceConversion[]>()

  useEffect(() => {
    refreshTokensList();
  }, [refreshTokensList, session]);

  useEffect(() => {
    if (!orderPayment) return;
    if (!userBalances) return;

    const conversionWithBalances: UserBalanceConversion[] = orderPayment.converted.reduce((prev:UserBalanceConversion[], current: TokenConversion) => {
      const balance = getUserBalanceForToken(current.symbol, userBalances);
      const converted:UserBalanceConversion = { ...current,balance:balance,enabled:balance > parseFloat(current.amount) };
      prev.push(converted);
      return prev

    }, []);
    const orderedBalances = conversionWithBalances.sort((a,b)=> +b.enabled - +a.enabled)
    setTokensWithUserBalances(orderedBalances);
  }, [orderPayment,userBalances]);


  if (!orderPayment && asyncStatus === "pending" ) return <Processing />
  if (!orderPayment) return <></>

  return (
    <div className="grid grid-cols-1 gap-6 relative bg-white p-4">
      { asyncStatus === "pending" && <Processing className="absolute right-0 top-0 bottom-0 left-0 bg-white bg-opacity-80" />}
      <div className="usd_cart_amount grid grid-cols-[1fr,min-content] items-center">
        <span className="text-sm font-bold text-gray-500">
          {orderPayment.base_amount} { orderPayment.base_currency} = {toPrecision(orderPayment.usd_amount, 2)} USD
        </span>
        <button
          className="text-sm font-bold text-gray-500 p-0 m-0 bg-transparent border-none focus:text-gray-500 focus:bg-transparent  hover:text-gray-500 hover:bg-transparent"
          onClick={() => refreshTokensList()}
        >
          Refresh{" "}
        </button>
      </div>
      <div>
        <span className="font-bold text-lg">
          Select the token you want to pay with
        </span>
        <ul className="grid grid-cols-1 gap-2 p-0 m-0 list-none">
          {tokensWithUserBalances &&
            tokensWithUserBalances.map((token, index) => {
              return (
                <li
                  key={index}
                  
                >
                  <TokenSelectRenderItem onClick={()=>processPayment(`${token.amount} ${token.symbol}`,token.contract)} token={token}></TokenSelectRenderItem>
                </li>
              );
            })}
        </ul>
      </div>
    </div>
  );
};
