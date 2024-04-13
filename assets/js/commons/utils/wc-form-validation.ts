export function validateCheckoutForm():boolean {
  
  let invalidCount = 0
  if (window && window.jQuery) {
    window.jQuery('.validate-required input, .validate-required select').trigger('validate');
    jQuery('.woocommerce-invalid').each(
      function (index, element) {
        if (element) invalidCount++
      }
    )
  } else {
    return true
  }
  localStorage.setItem('wookey-checkout-form-state',JSON.stringify({isValid:invalidCount == 0}))
  return invalidCount == 0

}