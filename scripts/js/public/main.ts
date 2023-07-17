import './app.css'
import App from './App.svelte'

const appWrapper = document.createElement('div');
appWrapper.setAttribute('id', 'proton-wc-app');
document.body.appendChild(appWrapper);

const app = new App({
  target: document.getElementById('proton-wc-app'),

})

export default app
