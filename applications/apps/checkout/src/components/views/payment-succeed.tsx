import { useCheckout } from "./../../providers/checkout-provider";
import React from "react";

export const PaymentSucceed = () => {

  const {config} = useCheckout()

  return (
    <div className="flex flex-col justify-center gap-8 bg-white p-4">
      <div className="flex-col md:grid md:grid-cols-[min-content,1fr] justify-start gap-4">
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
          className="lucide lucide-badge-check w-16 h-16 fill-green-400 stroke-white hidden md:block"
        >
          <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
          <path d="m9 12 2 2 4-4" />
        </svg>

        <div className="flex flex-col gap-4 ">
          <div>
            <div className="flex flex-col md:flex-row items-center">
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
                className="lucide lucide-circle-x w-16 h-16 fill-green-400 stroke-white block md:hidden"
              >
                <path d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                <path d="m9 12 2 2 4-4" />
              </svg>

              <span className="text-2xl font-bold text-green-400">Payment succeed</span>
            </div>
          </div>
          <div>
            <p>
              Thank you! You order is on the way. You can now view your order or close this window safely.  
            </p>
          </div>
        </div>
      </div>
      <div className="flex flex-col md:flex-row justify-end gap-4">
        <a href={ config?.wooThankYouUrl} className="p-2 bg-black rounded-md font-bold text-white hover:bg-brand hover:text-white text-center">
          Continue
        </a>
      </div>
    </div>
  );
};
