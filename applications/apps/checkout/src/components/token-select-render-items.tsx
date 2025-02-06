import React from "react";
import { getUserBalanceForToken, UserBalanceConversion } from "xprcheckout";
import { useCheckout } from "../providers/checkout-provider";
import classNames from "classnames";

type TokenSelectRenderItemProps = React.HTMLAttributes<HTMLDivElement> & {token:UserBalanceConversion}
/**
 * @component TokenSelectRenderItem
 * @description Renders a single token selection item with balance information
 * @param {Object} props - Component properties
 * @param {UserBalanceConversion} props.token - Token information including balance and conversion details
 * @param {Object} props.rest - Additional HTML div properties
 * @returns {JSX.Element} Token selection item with logo, amount, balance, and selection indicator
 */
export const TokenSelectRenderItem: React.FunctionComponent<TokenSelectRenderItemProps> = ({ token,...rest }) => {
  
  const { userBalances } = useCheckout()
  
  const rootClasses = classNames({
    'token-select-item-renderer':true,
    'grid grid-cols-[40px,1fr,min-content] items-center gap-4 select-none card  p-4': true,
    'hover:bg-white shadow-sm rounded-md hover:shadow-lg hover:z-20 border-2 border-gray-50 hover:border-brand':token.enabled,
    'opacity-30 disabled':!token.enabled
  })

  return (
    <div
      aria-roledescription="Select token"
      className={rootClasses}
      {...rest}
    >
      <img
        width="45"
        className="max-w-fit "
        alt={token.contract}
        src={token.logo}
      />
      <div className="flex flex-col">
        <span className="font-bold">
          {"Pay"} {token.amount} {token.symbol}
        </span>
        <span className="text-sm text-gray-500">
          Balance {getUserBalanceForToken(token.symbol, userBalances)}{" "}
          {token.symbol}
        </span>
      </div>

      <div>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="#"
          className="w-6 h-6"
        >
          <path d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" />
        </svg>
      </div>
    </div>
  );
};
