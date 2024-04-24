import App from "./App.svelte";
import  "./app.css";

const target = document.getElementById("wookey-checkout")
if (target) {
  
  new App({
    target: document.getElementById("wookey-checkout"),
  });
}



