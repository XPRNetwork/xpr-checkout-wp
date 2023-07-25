<script lang="ts">
  import { onMount } from 'svelte';
  import ProtonWeb, { type LinkSession, type TransactResult } from '@proton/web-sdk';
  import {MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS} from '../commons/constants/';
  import Dialog from '../commons/components/dialogs/Dialog.svelte';
  import PayTokenSelector from '../commons/components/dialogs/content/PayTokenSelector.svelte';
  import PaymentSucceed from '../commons/components/dialogs/content/PaymentSucceed.svelte';
  import PaymentVerify from '../commons/components/dialogs/content/PaymentVerify.svelte';
  import {generateTransferAction,generateRegisterPaymentAction} from '../commons/proton/actions/';
  import {APP_STATE_TOKEN_SELECT, APP_STATE_TRANSFER_VERIFICATION, APP_STATE_TRANSFER_VERIFICATION_FAILURE, APP_STATE_TRANSFER_VERIFICATION_SUCCESS} from './constants/';
  import {truncateToPrecision} from '../commons/utils/price'
  import type { ProtonCheckOutState, ProtonWCControllerOption, TokenRate } from '../commons/type';
  
  

  //if (!window['woowParams']) return;
  let wooCheckForm:HTMLElement | null = null
  let pluginOptions: ProtonWCControllerOption = window['woowParams'] as ProtonWCControllerOption;
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

  function onPaymentVerify (verifyResult:any){

    console.log(verifyResult,verifyResult.status)
    if(verifyResult && verifyResult.status==200){

      if (verifyResult.status == 200){
      if(verifyResult.body_response != null){
        pluginOptions.order = verifyResult.body_response;
        protonCheckoutState.appState = APP_STATE_TRANSFER_VERIFICATION_SUCCESS;
      }
    }else {
      protonCheckoutState.appState = APP_STATE_TRANSFER_VERIFICATION_FAILURE;
    }

    }

  }

  async function initTransfer (token:TokenRate,amount:number | string){

    if (!protonCheckoutState || !protonCheckoutState.session || !protonCheckoutState.isRunning) return 
    const registerPaymentAction = generateRegisterPaymentAction(
      pluginOptions.testnet ? pluginOptions.testwallet : pluginOptions.mainwallet ,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      pluginOptions.paymentKey,
      truncateToPrecision(amount,token.decimals),
      token.symbol,
      token.contract
      
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

    console.log([registerPaymentAction,transferAction]);
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
<main id="woow_payment_process">
  <Dialog>
    <div slot="head">
      <h3 class="modal_title">Thank you!</h3>
    </div>
    <div slot="content">
      <PaymentSucceed></PaymentSucceed>
      <!-- <PayTokenSelector cartAmount={pluginOptions.order.total.toString()} changeSession={changeAccount} selectPayToken={(token,amount)=>initTransfer(token,amount)} allowedTokens={pluginOptions.allowedTokens} actorName={protonCheckoutState.session.auth.actor.toString()} on:chan/> -->
    </div>
  </Dialog>

  {#if pluginOptions.order.status !== "completed"}
  <div class="process_starter">
    <h3 class="process_typography">Pay with webauth</h3>
    <p class="process_typography">Connect your webauth wallet to start the payment flow. </p>
    <a class="checkout-button button alt wc-forward wp-element-button" on:click={connectProton}>Connect webauth</a>
  </div>
  {:else}
  <div class="process_starter">
    <h3 class="process_typography">Payment succesfull</h3>
    <p class="process_typography">This order is marked as complete</p>
  </div>
  {/if}
  {#if protonCheckoutState.isRunning}
    <Dialog open={protonCheckoutState.appState == APP_STATE_TOKEN_SELECT}>
      <div slot="head">
        <h3 class="modal_title">Select token</h3>
      </div>
      <div slot="content">
        <PayTokenSelector storeCurrency={pluginOptions.wooCurrency} cartAmount={pluginOptions.order.total.toString()} changeSession={changeAccount} selectPayToken={(token,amount)=>initTransfer(token,amount)} allowedTokens={pluginOptions.allowedTokens} actorName={protonCheckoutState.session.auth.actor.toString()}/>
      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION}>
      <div slot="head">
        <h3 class="modal_title">Select token</h3>
      </div>
      <div slot="content">
        <PaymentVerify paymentKey={pluginOptions.paymentKey} transactionId={protonCheckoutState.tx.processed.id} onVerify={onPaymentVerify}></PaymentVerify>
      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_SUCCESS}>
      <div slot="head">
        <h3 class="modal_title">Payment verified</h3>
      </div>
      <div slot="content">
        <PaymentSucceed></PaymentSucceed>
      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_FAILURE}>
      <div slot="head">
        <h3 class="modal_title">Verification fails</h3>
      </div>
      <div slot="content">
        
      </div>
    </Dialog>
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

  .modal_title {

    padding: 0;
    margin: 0;

  }

</style>
