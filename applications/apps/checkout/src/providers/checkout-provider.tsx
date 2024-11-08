import React, {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useState,
} from "react";
import {useXPRN} from "xprnkit";
import {getUserBalances, OrderPayment, UserBalance} from "xprcheckout";
import {ServiceStatus} from "xprnkit/build/interfaces/service-status";
import {getOrderPayment} from "xprcheckout/services/OrderPayment";
import { xprcheckout } from "../interfaces/xprcheckout";
import { xtokens } from "../interfaces/xtokens";
import { CheckoutConfig } from "../global";

export enum APP_VIEWS {
  PENDING_LOGIN,
  TOKEN_SELECT,
  TRANSFER,
  VERIFY,
  SUCCESS,
  FAIL,
}

export type CheckoutProviderTypes = {
  setViewState: (state: APP_VIEWS) => void;
  refreshTokensList: () => void;
  processPayment: (amount:string,tokenContract:string) => void;
  lastError?: string;
  userBalances?: UserBalance[];
  viewState?: APP_VIEWS;
  config?: CheckoutConfig;
  orderPayment?: OrderPayment;
  asyncStatus: ServiceStatus;
};

const checkoutContext = createContext<CheckoutProviderTypes>({
  setViewState: (state: APP_VIEWS) => {},
  refreshTokensList: () => {},
  processPayment: () => {},
  lastError: "",
  userBalances: [],
  viewState: undefined,
  config: undefined,
  orderPayment: undefined,
  asyncStatus: "idle",
});

type CheckoutProviderProps = React.HTMLAttributes<HTMLDivElement> & {
  config: CheckoutConfig;
};
export const CheckoutProvider: React.FunctionComponent<
  CheckoutProviderProps
> = ({children, config}) => {
  const {session} = useXPRN();
  const [viewState, setViewState] = useState<APP_VIEWS>();
  const [userBalances, setUserBalances] = useState<UserBalance[]>();
  const [asyncStatus, setAsyncStatus] = useState<ServiceStatus>();
  const [orderPayment, setOrderPayment] = useState<OrderPayment>();
  const [lastError,setLastError] = useState<string>();

  const refreshTokensList = useCallback(() => {
    if (!session) return;
    setAsyncStatus("pending");
    (async () => {
      const userBalances = await getUserBalances(
        session.auth.actor.toString(),
        true
      );
      setUserBalances(userBalances);
      const paymentOrder = await getOrderPayment(
        config.baseDomain,
        config.requestedPaymentKey
      );
      setOrderPayment(paymentOrder);
      if (paymentOrder.verified) setViewState(APP_VIEWS.SUCCESS);
    })().then(res => {
      setAsyncStatus("success");
    });
  }, [config, session,setViewState,setAsyncStatus]);

  const processPayment = useCallback((amount:string,tokenContract:string) => {
    if (!session) return;
    const regPaymentAction = xprcheckout.pay_reg(
      [
        {
          actor: session.auth.actor.toString(),
          permission: session.auth.permission.toString(),
        },
      ],
      {
        paymentKey: config.requestedPaymentKey,
        buyer: session.auth.actor.toString(),
        storeAccount:config.store
      }
    );

    const transferAction = xtokens.transfer([
      {
        actor: session.auth.actor.toString(),
        permission: session.auth.permission.toString(),
      },
    ],
      {
      
        from: session.auth.actor.toString(),
        to: 'xprcheckout',
        quantity: amount,
        memo: `${config.requestedPaymentKey}:${tokenContract}`
      });
    (transferAction as any).account = tokenContract;
    try {
      setViewState(APP_VIEWS.VERIFY);
       session.transact({ actions: [regPaymentAction, transferAction] }, { broadcast: true }).then(() => {
        refreshTokensList()
      }).catch((e) => {
        setLastError(e.toString());
        setViewState(APP_VIEWS.FAIL)
      })
    } catch (e: any) {
      setViewState(APP_VIEWS.FAIL);
    }

  }, [session,config,refreshTokensList,setViewState]);

  return (
    <checkoutContext.Provider
      value={{
        refreshTokensList,
        processPayment,
        setViewState,
        lastError,
        userBalances: userBalances,
        viewState,
        config,
        orderPayment,
        asyncStatus: asyncStatus ? asyncStatus : "idle",
      }}
    >
      {children}
    </checkoutContext.Provider>
  );
};

export function useCheckout() {
  return useContext(checkoutContext);
}
