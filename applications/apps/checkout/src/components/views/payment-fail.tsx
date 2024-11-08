import React from "react";
import {APP_VIEWS, useCheckout} from "../../providers/checkout-provider";

export const PaymentFail = () => {
  const {setViewState, lastError} = useCheckout();
  return (
    <div className="flex flex-col justify-center gap-8 bg-white p-4">
      <div className="flex-col md:grid md:grid-cols-[min-content,1fr] justify-start gap-4">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          strokeWidth="2"
          strokeLinecap="round"
          strokeLinejoin="round"
          className="lucide lucide-circle-x w-16 h-16 fill-red-400 stroke-white hidden md:block"
        >
          <circle cx="12" cy="12" r="10" />
          <path d="m15 9-6 6" />
          <path d="m9 9 6 6" />
        </svg>
        <div className="flex flex-col gap-4 ">
          <div>
            <div className="flex flex-col md:flex-row  items-center">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
                className="lucide lucide-circle-x w-16 h-16 fill-red-400 stroke-white block md:hidden"
                >
                <circle cx="12" cy="12" r="10" />
                <path d="m15 9-6 6" />
                <path d="m9 9 6 6" />
              </svg>
                
              <span className="text-2xl font-bold text-red-400">Payment fail</span>
            </div>
          </div>
          <div>
            <p className="italic font-bold text-sm text-gray-500">
              {lastError}
            </p>
            <p>
              But don't worry, nothing wrong, your funds and order are safe! You
              can retry the process as you want or save it to pay later.
            </p>
          </div>
        </div>
      </div>
      <div className="flex flex-col md:flex-row justify-end gap-4">
        <button
          className="p-2 bg-brand rounded-md font-bold text-white "
          onClick={() => setViewState(APP_VIEWS.TOKEN_SELECT)}
        >
          Retry
        </button>
        <button
          className="p-2 border-brand border-2 rounded-md font-bold text-brand "
          
        >
          Save
        </button>
      </div>
    </div>
  );
};
