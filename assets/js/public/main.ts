import App from "./App.svelte";

let app = new App({
  target: document.getElementById("wookey-checkout"),
});
if (window.jQuery) {
  updateOrderButton()
  window.jQuery(document.body).on("updated_checkout", () => {
    let app = new App({
      target: document.getElementById("wookey-checkout"),
    });
    updateOrderButton()
  });
}

function updateOrderButton() {
  const methodRadio = document.querySelectorAll(
    ".wc_payment_method .input-radio"
  );
  const checkoutButton = document.querySelector(
    'button[name="woocommerce_checkout_place_order"]'
  );
  Array.prototype.slice.call(methodRadio).map(r => {
    if (checkoutButton) { 

      checkoutButton.style.display = r.value == "wookey" ? "none" : "block";
    }
    r.addEventListener("change", e => {
      if (e.target) {
        
        if (checkoutButton) {
          checkoutButton.style.display =
            e.target.value == "wookey" ? "none" : "block";
        }
      }
    });
  });
}

export default app;
