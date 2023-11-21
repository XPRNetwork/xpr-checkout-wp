<script lang="ts">
  import type {  TokenRate, PaymentVerifyResponse, WPResponse } from '../commons/type';
  import './../../styles/base.scss'
  import type { ConfigWithCart, ProtonCheckOutState,  } from './public.type';
  import { onMount } from 'svelte';
  import Dialog from '../commons/components/dialogs/Dialog.svelte';
  import PayTokenSelector from '../commons/components/dialogs/content/PayTokenSelector.svelte';
  import PaymentSucceed from '../commons/components/dialogs/content/PaymentSucceed.svelte';
  import PaymentVerify from '../commons/components/dialogs/content/PaymentVerify.svelte';
  import {webauthConnect} from '../commons/proton/connect';
  import {generateTransferAction,generateRegisterPaymentAction} from '../commons/proton/actions/';
  import {APP_STATE_TOKEN_SELECT, APP_STATE_TRANSFER_VERIFICATION, APP_STATE_TRANSFER_VERIFICATION_FAILURE, APP_STATE_TRANSFER_VERIFICATION_SUCCESS} from './constants/';
  import {truncateToPrecision,canRestoreSession} from '../commons/utils/index'
  import { getCart } from '../commons/services/Cart';
  import type { TransactResult } from '@proton/web-sdk';
  
  let params: ConfigWithCart = window['params'] as ConfigWithCart;
  let protonCheckoutState:ProtonCheckOutState = {isRunning:false};
  let checkoutForm = null

  onMount(async ()=>{

    
    if(canRestoreSession())connectProton(true,true);
    const updatedCartSession = await getCart(params.baseDomain); 
    checkoutForm = document.querySelector('form[name="checkout"]')
    params.cartSession = updatedCartSession.data.body_response;
    console.log(params)
    

  })

  async function connectProton(restoreSession = false,silentRestore=false) {
    
    console.log("connect proton")
    if (protonCheckoutState.session)  {
        protonCheckoutState.isRunning = true;
        if (!silentRestore)protonCheckoutState.appState = APP_STATE_TOKEN_SELECT
        return protonCheckoutState.session;
    };
    const session = await webauthConnect(
      params.testnet ? params.testnetActor : params.mainnetActor,
      params.appName,
      params.testnet,
      restoreSession 
    )
    protonCheckoutState.isRunning = !!session
    if (session) {
      protonCheckoutState.session = session
      if (!silentRestore)protonCheckoutState.appState = APP_STATE_TOKEN_SELECT
    }
    return session

  }

  function changeAccount (){

    protonCheckoutState.isRunning = false
    protonCheckoutState.session = undefined;
    connectProton(false)

  }

  function onPaymentVerify (verifyResult:WPResponse<PaymentVerifyResponse>){

    console.log(verifyResult,verifyResult.status)
    if(verifyResult && verifyResult.status==200){

      if (verifyResult.status == 200){
      if(verifyResult.body_response.validated){
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
      params.testnet ? params.testnetActor : params.mainnetActor ,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      params.cartSession.paymentKey,
      truncateToPrecision(amount,token.decimals),
      token.symbol,
      token.contract
      
    )
    const transferAction = generateTransferAction(
      token.contract,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      "wookey",
      truncateToPrecision(amount,token.decimals),
      token.symbol,
      params.cartSession.paymentKey
    )

    protonCheckoutState.isRunning = false;
    try {
      
      const tx:TransactResult = await protonCheckoutState.session.transact(
      {
        actions:[
          registerPaymentAction,
          transferAction
        ]
      },
      {
        broadcast:true

      }
    )

    protonCheckoutState.tx = tx;
    protonCheckoutState.appState = APP_STATE_TRANSFER_VERIFICATION;
    protonCheckoutState.isRunning = true;

    }catch (e:any){

      console.log("Error occur")
      protonCheckoutState.isRunning = false
      protonCheckoutState.appState = undefined
      

    } 
  }

  function setAppState(appState:string){

    protonCheckoutState.appState = appState

  }

</script>
<main id="wookey-checkout" class="wookey-app wookey-app__grid">
  
  
    <h3>{params.translations.payInviteTitle}</h3>
    <p>{params.translations.payInviteText}</p>
    {#if protonCheckoutState.session}
    <a class="woow-button checkout-button button alt wc-forward wp-element-button" on:click={()=>connectProton(true,false)}>Pay as {protonCheckoutState.session.auth.actor.toString()}</a>
    {:else}
    <a class="woow-button checkout-button button alt wc-forward wp-element-button" on:click={()=>connectProton()}>{params.translations.payInviteButtonLabel}</a>
    {/if}
  {#if protonCheckoutState.isRunning}
    <Dialog classes="select__token__dialog__content" open={protonCheckoutState.appState == APP_STATE_TOKEN_SELECT}>
      <div slot="head">
        <h3>{params.translations.selectTokenDialogTitle}</h3>
        <p>{params.translations.selectTokenDialogText}</p>
      </div>
      <div slot="content">
        <PayTokenSelector 
        storeCurrency={params.wooCurrency} 
        cartAmount={params.cartSession.cartTotal.toString()} 
        changeSession={changeAccount} 
        selectPayToken={(token,amount)=>initTransfer(token,amount)} 
        allowedTokens={params.allowedTokens} 
        actorName={protonCheckoutState.session.auth.actor.toString()}
        baseDomain={params.baseDomain}
        translations={{
          processingLabel:params.translations.selectTokenPayProcessingLabel,
          payLabel:params.translations.selectTokenPayButtonLabel,
          connectedAdLabel:params.translations.selectTokenDialogConnectedAs,
          changeAccountLabel:params.translations.selectTokenDialogChangeAccountLabel
        }}
        />

      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION}>
      <div slot="head">
        <h3>{params.translations.verifyPaymentDialogTitle}</h3>
      </div>
      <div slot="content">
        <PaymentVerify 
        network={params.testnet?'testnet':'mainnet'}
        paymentKey={params.cartSession.paymentKey} 
        transactionId={protonCheckoutState.tx.processed.id} 
        onVerify={onPaymentVerify}
        baseDomain={params.baseDomain}
        translations={{
          processingLabel:params.translations.verifyPaymentDialogProcessLabel,
          verifyText:params.translations.verifyPaymentDialogText
        }}
        ></PaymentVerify>
      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_SUCCESS}>
      <div slot="head">
        <h3>{params.translations.verifySuccessPaymentDialogTitle}</h3>
      </div>
      <div slot="content">
        <PaymentSucceed translations={{text:params.translations.verifySuccessPaymentDialogText}}></PaymentSucceed>
      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_FAILURE}>
      <div slot="head">
        <h3>Verification fails</h3>
      </div>
      <div slot="content">
        
      </div>
    </Dialog>
  {/if}
</main>

<style>
   :global( [slot="content"] ) {

    max-height: 100%;
    display: grid;
    grid-template-rows: min-content 1fr min-content;
    gap:10px;

  }
</style>
