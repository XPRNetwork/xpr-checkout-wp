import React, {useCallback} from "react";
import {useXPRN} from "xprnkit";
import {RefundConfig} from "../global";
import { xtokens } from "../interfaces/xtokens";
import { pushRefund } from "../services/refund-service";
import { xprcheckout } from "../interfaces/xprcheckout";


type RefundButtonProps = React.HTMLAttributes<HTMLButtonElement> & {
  config: RefundConfig;
};
export const RefundButton: React.FC<RefundButtonProps> = props => {
  const { session,connect } = useXPRN();
  
  const onConnect = useCallback((e:React.MouseEvent) => {
    e.preventDefault();
    connect()

  }, [connect])
  
  const onRefund = useCallback((e: React.MouseEvent) => {
    e.preventDefault();
    if (!session) return;
    
    const refundTransferAction = xtokens.transfer(
      [
        {
          actor: session.auth.actor.toString(),
          permission: session.auth.permission.toString(),
        },
      ],
      {
        from: session.auth.actor.toString(),
        to: props.config.accountToRefund,
        quantity: props.config.amountToRefund,
        memo:`Refund: ${props.config.requestedPaymentKey}`
      }
    );

    const refundAction = xprcheckout.pay_refund([
      {
        actor: session.auth.actor.toString(),
        permission: session.auth.permission.toString(),
      },
    ], {
      paymentKey: props.config.requestedPaymentKey,
      storeAccount:props.config.store
    })
    //(refundAction as any).account = 'eosio.token';
    
    session.transact({ actions: [refundTransferAction,refundAction] }).then(() => {
      pushRefund(props.config.baseDomain, props.config.requestedPaymentKey, props.config.adminNonce).then(() => {
      window.location.reload();
      
    })  
      
    })
  }, [session, props]);

  return (
    <>
      {!session ? (
        <button onClick={(e) => { onConnect(e) }} className="p-4 text-white bg-black rounded-md w-full">Connect @{props.config.store} to refund { props.config.amountToRefund} to @{ props.config.accountToRefund}</button>
    ) :(  <button onClick={(e)=>{onRefund(e)}} className="p-4 text-white bg-brand rounded-md w-full">
        Refund {props.config.amountToRefund} to {props.config.accountToRefund}
    </button>)}      
    </>
    
        
  );
};
