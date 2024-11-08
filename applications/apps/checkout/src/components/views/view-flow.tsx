import React from 'react'
import { APP_VIEWS, useCheckout } from "../../providers/checkout-provider"
import { PendingLoginView } from "./pending-login"
import { CheckoutTokenSelect } from './token-select'
import { PaymentSucceed } from './payment-succeed'
import { PaymentFail } from './payment-fail'
import { Processing } from '../processing'
import { VerifyPayment } from './verify-payment'

export const ViewFlow = () => {
  const { viewState } = useCheckout()

  const renderView = () => {
    console.log(viewState,'view state')
    switch (viewState) {
      case APP_VIEWS.TOKEN_SELECT:
        return <CheckoutTokenSelect />
      case APP_VIEWS.TRANSFER:
        return <><Processing></Processing></>
      case APP_VIEWS.SUCCESS:
        return <PaymentSucceed />
      case APP_VIEWS.FAIL:
        return <PaymentFail />
      case APP_VIEWS.VERIFY:
        return <VerifyPayment />
      default:
        return <PendingLoginView />
    }
  }

  return (
    <div>
      {renderView()}
    </div>
  )
}
  