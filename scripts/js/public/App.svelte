<script lang="ts" context="module">
  export interface TokenRate{
  contract: string
  decimals: number
  symbol: string
  id: string
  system_price: number
  usd_price: number
}
</script>
<script lang="ts">
  import { onMount } from 'svelte';
  import ProtonWeb, { type LinkSession, type TransactResult } from '@proton/web-sdk';
  import {truncateToPrecision} from './utils/price'
  import {APP_STATE_TOKEN_SELECT, APP_STATE_TRANSFER_VERIFICATION, MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS, WOO_CHECKOUT_FORM_SELECTOR} from './constants';
  import PayTokenSelector from './PayTokenSelector.svelte';
  import {generateTransferAction,generateRegisterPaymentAction} from './proton/actions/'
  
  interface ProtonWCControllerOption {

    mainwallet?:string;
    testwallet?:string;
    testnet?:boolean;
    appName?:string;
    appLogo?:string;
    allowedTokens?:string;
    wooCurrency?:string;
    paymentKey:string;
    order:{
      total:number
    }

  }

  interface ProtonCheckOutState {

    appState?:string;
    isRunning:boolean,
    session?:LinkSession
    tx?:TransactResult

  }

  
  let wooCheckForm:HTMLElement | null = null
  let pluginOptions: ProtonWCControllerOption = window.woowParams! as ProtonWCControllerOption;
  let txId: string | undefined = undefined;
  let protonCheckoutState:ProtonCheckOutState = {isRunning:false};

  onMount(()=>{

    console.log(pluginOptions)

  })

  async function  connectProton() {
    
    if (protonCheckoutState.session)  {
        protonCheckoutState.isRunning = true
        return protonCheckoutState.session;
    };
    const { session, link } = await ProtonWeb({
      linkOptions: {
        chainId: pluginOptions.testnet ? TESTNET_CHAIN_ID : MAINNET_CHAIN_ID,
        endpoints: pluginOptions.testnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS,
      },
      transportOptions: {
        requestAccount: pluginOptions.testnet ? pluginOptions.testwallet : pluginOptions.mainwallet, 
      },
      selectorOptions: {
        appName: pluginOptions.appName,
      }
    })
    protonCheckoutState.isRunning = !!session
    if (session) {
      protonCheckoutState.session = session
      protonCheckoutState.appState = APP_STATE_TOKEN_SELECT
    }
    return session

  }

  function closeCheckoutModal (){

    protonCheckoutState.isRunning = false

  }
  
  function changeAccount (){

    protonCheckoutState.isRunning = false
    protonCheckoutState.session = undefined;
    connectProton()

  }

  async function initTransfer (token:TokenRate,amount:number){

    if (!protonCheckoutState || !protonCheckoutState.session || !protonCheckoutState.isRunning) return 
    const registerPaymentAction = generateRegisterPaymentAction(
      pluginOptions.testnet ? pluginOptions.testwallet : pluginOptions.mainwallet ,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      pluginOptions.paymentKey,
      truncateToPrecision(amount,token.decimals),
      token.symbol,
      
    )
    const transferAction = generateTransferAction(
      token.contract,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      "woow",
      truncateToPrecision(amount,token.decimals),
      token.symbol,
      pluginOptions.paymentKey
    )

    console.log([registerPaymentAction,transferAction])

    protonCheckoutState.isRunning = false;
    const tx:TransactResult = await protonCheckoutState.session.transact(
      {
        actions:[registerPaymentAction,transferAction]
      },
      {
        broadcast:true
      }
    )

    protonCheckoutState.tx = tx;
    protonCheckoutState.appState = APP_STATE_TRANSFER_VERIFICATION;
    protonCheckoutState.isRunning = true;


  }



</script>
<main id="proton_wc_checkout">
  <div class="process_starter">
    <h3 class="process_typography">Pay with webauth</h3>
    <p class="process_typography">Connect your webauth wallet to start the payment flow. </p>
    <button on:click={connectProton}>Connect webauth</button>
  </div>
  {#if protonCheckoutState.isRunning}
  <div class="process_wrapper">
    <div class="process_modal__backdrop"></div>
    {#if protonCheckoutState.appState == APP_STATE_TOKEN_SELECT}
    <div class="process_modal__content">
      <div class="process_modal__content__head">
        <h3 class="modal_title">Select checkout token</h3>
        <button on:click={closeCheckoutModal} class="modal_close"></button>
      </div>
      <div class="process_modal__content__body">
        <span class="modal_detail">Choose the token you want to pay with through webauth.</span>
        <PayTokenSelector cartAmount={pluginOptions.order.total} changeSession={changeAccount} selectPayToken={(token,amount)=>initTransfer(token,amount)} allowedTokens={pluginOptions.allowedTokens} actorName={protonCheckoutState.session.auth.actor.toString()} on:chan/>
      </div>
    </div>
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION}
    <div class="process_modal__content">
      <div class="process_modal__content__head">
        <h3 class="modal_title">Processing payment</h3>
        <button on:click={closeCheckoutModal} class="modal_close"></button>
      </div>
      <div class="process_modal__content__body">
        <span class="modal_detail">Please wait while we check payment information.</span>
      </div>
      <div class="process_modal__content__body">
          <div class="processing">
            <div class="processing__icon"></div>
          </div>
      </div>
    </div>
    {/if}
  </div>
  
  
  {/if}
</main>

<style>

.process_typography {

    padding: 0;
    margin: 0;

  }
  .process_starter {

    border: 1px solid;
    display: grid;
    grid-template-columns: 1fr;
    padding: 20px;
    gap: 20px;

  }
  .process_wrapper {

    display: block;
    position:fixed;
    top: 0;
    left: 0;
    right:0;
    bottom:0;
    z-index: 2000;
    
  }

  .process_modal__backdrop {
    position:absolute;
    top: 0;
    left: 0;
    right:0;
    bottom:0;
    background-color: rgba(0,0,0,0.5);
    z-index: 1;
  }

  .process_modal__content {
    position:absolute;
    left:50%;
    top: 50%;
    max-height: 80vh;
    width: 30%;
    transform: translate(-50%,-50%);
    background-color: white;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    padding: 10px;
    
  }

  .process_modal__content__head {

    display: grid;
    grid-template-columns: 1fr min-content;
    gap: 10px;
    padding: 10px;
    align-items: center;
    
  }
  
  .process_modal__content__body {

    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    padding: 10px;

  }

  .processing {

    width: 100%;
    display: grid;
    justify-content: center;
    align-items: center;

  }
  
  .processing__icon {

    width:80px;
    height:80px;
    background-image: url('../../public/img/process_icon.png');
    background-position: center center;
    background-size: contain;
    background-repeat: no-repeat;
    padding: 0;
    margin: 0;
    border-radius: 0;
    animation: process 1s infinite;


  }

  .modal_title {

    padding: 0;
    margin: 0;

  }
  
  .modal_detail {

    padding: 0;
    margin: 0;

  }

  .modal_close {

    width: 30px;
    height: 30px;
    background-image: url('../../public/img/close_icon.png');
    background-position: center center;
    background-size: contain;
    background-repeat: no-repeat;
    padding: 0;
    margin: 0;
    border-radius: 0;
    background-color: transparent;

  }

  @keyframes process {

    from{
      transform:rotate(0deg)
    }
    
    to{
      transform:rotate(360deg)
    }


  }



</style>
