import React from "react";
import { useCheckout } from "../providers/checkout-provider";

export const AppFooter = () => {

  const { config } = useCheckout();
  
  return (
    <div className="flex flex-col justify-center items-center my-8 ">
      <a href={config?.wooCheckoutUrl} className={`btn btn-sm btn-outline rounded-md underline`}>Cancel payment</a>
    </div>
  );
};
