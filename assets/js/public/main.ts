import App from './App.svelte'

let app = new App({
  target: document.getElementById('wookey-checkout'),

})
if (window.jQuery) {

  window.jQuery( document.body ).on('updated_checkout', () => {
    const elm = document.getElementById('wookey-checkout')
    if (elm)elm.innerHTML = '';
    console.log('the checkout mfer is called');
    app = new App({
      target: document.getElementById('wookey-checkout'),
    
    })
  
  })
  
}


export default app
