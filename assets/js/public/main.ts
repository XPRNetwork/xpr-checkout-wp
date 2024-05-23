import App from "./App.svelte";
import  "./app.css";

const target = document.getElementById("xprcheckout-checkout")
if (target) {
  
  new App({
    target: document.getElementById("xprcheckout-checkout"),
  });
}



