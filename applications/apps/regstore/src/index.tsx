import React from 'react';
import ReactDOM from 'react-dom/client';
import './index.css';
import App from './App';
import reportWebVitals from './reportWebVitals';

(window as any).xprcheckoutOptions = {
  isTestnet:true,
  networkCheckBoxSelector:"",
  testnetAccountFieldSelector:"",
  mainnetAccountFieldSelector:"",
  mainnetActor:"rockerone",
  testnetActor:"solid",  
  mainnetAccessToken:"qqdvdf6v54e6r54v65qf4v65a4zrt4bv65a4er654v",
  testnetAccessToken:"6qv5465z46zr3v1a3r2e1v654e64ca4r65v4",  
}

const root = ReactDOM.createRoot(
  document.getElementById('root') as HTMLElement
);
root.render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);

// If you want to start measuring performance in your app, pass a function
// to log results (for example: reportWebVitals(console.log))
// or send to an analytics endpoint. Learn more: https://bit.ly/CRA-vitals
reportWebVitals();
