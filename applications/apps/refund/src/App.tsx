import "./App.css";
import React, {useCallback, useEffect, useState} from "react";
import {XPRNProvider} from "xprnkit";
import {RefundButton} from "./components/refund-button";
import {
  PaymentStatus,
  verifyPaymentStatus,
} from "./services/verify-payment-status";
import {ServiceStatus} from "xprnkit/build/interfaces/service-status";
import { wait } from "xprcheckout";

const pluginConfig = window.pluginConfig;

function App() {
  const [refunded, setRefunded] = useState<string>();
  const [settlementStatus, setSettlementStatus] = useState<PaymentStatus>();
  const [verificationState, setVerificationState] = useState<ServiceStatus>();

  const onPaymentStatusChange = useCallback(async ():Promise<PaymentStatus> => {
    setVerificationState("pending");
    await wait(5000);
    return await verifyPaymentStatus(
      pluginConfig.requestedPaymentKey,
      pluginConfig.endpoints.split(",")
    ).then(res => {
      setSettlementStatus(res);
      setVerificationState("success");
      return res
    })
  },[])

  useEffect(() => {
    (async () => {
      setRefunded(pluginConfig.orderStatus);
      onPaymentStatusChange()
    })()
    
  }, [onPaymentStatusChange]);

  return (
    <div>
      <XPRNProvider
        config={{
          apiMode: pluginConfig.gatewayNetwork,
          chainId: pluginConfig.chainId,
          endpoints: pluginConfig.endpoints.split(","),
          dAppName: "XPRCheckout",
          requesterAccount: "xprcheckout",
        }}
      >
        <div className="App">
          {verificationState === "pending" && (
            <div>
              
              <span>
                Verify order and payment
              </span>
            </div>
          )}
          {verificationState === "success" &&
            (refunded === "processing" || refunded === "completed") &&
            settlementStatus === "paid" && (
              <RefundButton config={pluginConfig} ></RefundButton>
            )}
          {verificationState === "success" &&
            refunded === "refunded" &&
            settlementStatus === "refunded" && (
              <div className="flex flex-col gap-2">
                <span className="font-bold text-xl">Refunded</span>
                <p>
                  Order have been refunded to {pluginConfig.accountToRefund}
                </p>
              </div>
            )}
          {verificationState === "success" && refunded === "refunded" && (
            <>
              {settlementStatus === "paid" && (
                <div className="flex flex-col gap-2">
                  <span className="font-bold text-xl">Status miss match</span>
                  <p>
                    Order is {pluginConfig.orderStatus}, but contract say{" "}
                    {settlementStatus}{" "}
                  </p>
                  <RefundButton config={pluginConfig}></RefundButton>
                </div>
              )}
              {settlementStatus === "pending" && (
                <div className="flex flex-col gap-2">
                  <span className="font-bold text-xl">Status miss match</span>
                  <p>
                    Order is {pluginConfig.orderStatus}, but contract say{" "}
                    {settlementStatus}{" "}
                  </p>
                </div>
              )}
            </>
          )}
        </div>
      </XPRNProvider>
    </div>
  );
}

export default App;
