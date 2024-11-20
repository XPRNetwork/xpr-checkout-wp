import React from "react";
import logo from './../assets/brand_logo.png'
import {useXPRN, XPRNAvatar} from "xprnkit";
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from "./dialog";
import { APP_VIEWS, useCheckout } from "../providers/checkout-provider";
export const AppHeader = () => {
  const {session, profile, disconnect, connect} = useXPRN();
  const { setViewState ,config} = useCheckout();

  return (
    <div className="flex flex-col  my-8 ">
      <div className="grid grid-cols-[1fr,max-content] gap-4 items-center w-full">
        <div className="grid grid-cols-[max-content,1fr] items-center justify-center gap-2">
          <div className="rounded-full overflow-hidden border-2 md:border-4 border-brand">
            <img
              className="w-8 h-8 md:w-12 md:h-12"
              src={logo}
              alt=""
            ></img>
          </div>
          <div className="flex flex-col">
            <span className="text-2xl md:text-4xl font-extrabold text-brand leading-4">
              XPRCHECKOUT ?
            </span>
            {config && config.gatewayNetwork === 'testnet' &&
            <span className="text-sm text-gray-500">Using Testnet</span>
            }
          </div>
        </div>
        {session && (
          <Dialog>
            <DialogTrigger className="text-sm flex items-center border-none focus:bg-transparent hover:bg-transparent">
                <XPRNAvatar className="w-10 h-10 rounded-full overflow-hidden bg-brand text-white font-bold text-xl uppercase" />
            </DialogTrigger>
            <DialogContent>
              <DialogHeader>
                <DialogTitle>Account</DialogTitle>
                <DialogDescription className="flex flex-col gap-4 pt-4">
                  <div className="flex gap-2 items-center">
                    <XPRNAvatar className="w-12 h-12 rounded-full overflow-hidden bg-brand text-white font-bold text-xl uppercase" />
                    <div>
                      <span className="text-lg font-bold">{ profile ? profile.displayName : session.auth.actor.toString()}</span>
                      <span className="text-md">@{ session.auth.actor.toString()}</span>

                    </div>
                  </div>
                  <div className="flex flex-col gap-2 ">
                    <button className="p-4 bg-brand text-white rounded-md w-full font-bold" onClick={() => {
                      disconnect();
                      setViewState(APP_VIEWS.PENDING_LOGIN)
                      connect(false, false, () => {
                        setViewState(APP_VIEWS.TOKEN_SELECT)
                      })
                    }}>Change account</button>
                    <button className="p-4 border-brand border-2 text-brand rounded-md w-full font-bold" onClick={() => {
                      disconnect();
                      setViewState(APP_VIEWS.PENDING_LOGIN)
                    }}>Log out</button>
                  </div>
                </DialogDescription>
              </DialogHeader>
            </DialogContent>
          </Dialog>
        )}
      </div>
    </div>
  );
};
