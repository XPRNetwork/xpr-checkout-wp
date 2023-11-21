import App from './App.svelte'

let app = new App({
  target: document.getElementById('wookey-checkout'),

})
if (window.jQuery) {

  window.jQuery(document.body).on('updated_checkout', () => {
    const checkoutButton = document.querySelector('button[name="woocommerce_checkout_place_order"]');
    if (checkoutButton) {
      checkoutButton.style.display = "none"
    }
    console.log(checkoutButton,"?")
    const elm = document.getElementById('wookey-checkout')
    if (elm)elm.innerHTML = '';
    
    app = new App({
      target: document.getElementById('wookey-checkout'),
    
    })
  
  })
  
}


export default app
