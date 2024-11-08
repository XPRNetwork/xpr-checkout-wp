import React from "react";
import classNames from "classnames";
import { APP_VIEWS, useCheckout } from "../providers/checkout-provider";
export const Stepper = () => {

  const {viewState} = useCheckout()
  const connectClasses = classNames({
    'w-8 h-8 bg-grey  rounded-full flex justify-center items-center text-sm':true,
    'bg-gray-200':!viewState || (viewState && viewState < APP_VIEWS.PENDING_LOGIN),
    'bg-brand text-white':viewState===APP_VIEWS.PENDING_LOGIN,
    'bg-white border-brand border-2 text-brand':viewState && viewState > APP_VIEWS.PENDING_LOGIN,
  })
  const connectClassesText = classNames({
    'font-bold md:block hidden ml-3': true,
    'text-brand':viewState===APP_VIEWS.PENDING_LOGIN,
    
  })
  
  const tokenSelectClasses = classNames({
    'w-8 h-8 bg-grey rounded-full flex justify-center items-center text-sm':true,
    'bg-gray-200':!viewState || (viewState && viewState < APP_VIEWS.TOKEN_SELECT),
    'bg-brand text-white':viewState===APP_VIEWS.TOKEN_SELECT,
    'bg-white border-brand border-2 text-brand':viewState && viewState > APP_VIEWS.TOKEN_SELECT,
  })

  const tokenSelectClassesText = classNames({
    'font-bold md:block hidden ml-3': true,
    'text-brand':viewState===APP_VIEWS.TOKEN_SELECT,
    
  })

  const verifyClasses = classNames({
    'w-8 h-8 bg-grey  rounded-full flex justify-center items-center text-sm':true,
    'bg-gray-200':!viewState || (viewState && (viewState < APP_VIEWS.VERIFY )),
    'bg-brand text-white':viewState===APP_VIEWS.VERIFY || (viewState && viewState>=APP_VIEWS.FAIL) || (viewState && viewState>= APP_VIEWS.SUCCESS),
    
  })

  const verifyClassesText = classNames({
    'font-bold md:block hidden ml-3': true,
    'text-brand':viewState===APP_VIEWS.VERIFY || viewState===APP_VIEWS.FAIL ||  viewState=== APP_VIEWS.SUCCESS,
    
  })

  return (
    <div className="bg-white w-full p-4 ">
      
          <ol className="grid grid-cols-[1fr,1fr,max-content] items-center w-full text-sm text-gray-500 font-medium sm:text-base">
            <li className="flex text-sm md:w-full items-center after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:inline-block after:mx-4 xl:after:mx-8 ">
              <div className="flex items-center whitespace-nowrap">
                <span
                  className={connectClasses}
                >
                  1
                </span>
                <p className={connectClassesText}>
                  Connect Webauth
                </p>
              </div>
            </li>
            <li className="flex text-sm md:w-full items-center after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:inline-block after:mx-4 xl:after:mx-8 ">
              <div className="flex items-center whitespace-nowrap ">
                <span
                  className={tokenSelectClasses}
                >
                  2
                </span>
                <p className={tokenSelectClassesText}>
                  Pay with token
                </p>
              </div>
            </li>
            
            <li className="flex text-sm items-center text-gray-400 ">
              <div className="flex items-center  ">
                <span
                  className={verifyClasses}
                >
                  3
                </span>
                <p className={verifyClassesText}>
                  Verify payment
                </p>
              </div>
            </li>
          </ol>
        </div>
  )
}