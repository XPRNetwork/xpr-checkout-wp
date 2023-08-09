<script lang="ts">
  import type { TokenRate } from '../commons/type';
  import './../../styles/base.scss'
  import type { ProtonCheckOutState, ProtonWCControllerOption,  } from './public.type';
  import { onMount } from 'svelte';
  
  import {LINK_STORAGE_PREFIX, MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS} from '../commons/constants/';
  import Dialog from '../commons/components/dialogs/Dialog.svelte';
  import PayTokenSelector from '../commons/components/dialogs/content/PayTokenSelector.svelte';
  import PaymentSucceed from '../commons/components/dialogs/content/PaymentSucceed.svelte';
  import PaymentVerify from '../commons/components/dialogs/content/PaymentVerify.svelte';
  import {webauthConnect} from '../commons/proton/connect';
  import {generateTransferAction,generateRegisterPaymentAction} from '../commons/proton/actions/';
  import {APP_STATE_TOKEN_SELECT, APP_STATE_TRANSFER_VERIFICATION, APP_STATE_TRANSFER_VERIFICATION_FAILURE, APP_STATE_TRANSFER_VERIFICATION_SUCCESS} from './constants/';
  import {truncateToPrecision,canRestoreSession} from '../commons/utils/index'
  
  let pluginOptions: ProtonWCControllerOption = window['wookeyCheckoutParams'] as ProtonWCControllerOption;
  let protonCheckoutState:ProtonCheckOutState = {isRunning:false};

  onMount(()=>{

    if(canRestoreSession())connectProton(true,true);

  })

  async function connectProton(restoreSession = false,silentRestore=false) {
    
    if (protonCheckoutState.session)  {
        protonCheckoutState.isRunning = true
        return protonCheckoutState.session;
    };
    const session = await webauthConnect(
      pluginOptions.testnet ? pluginOptions.testwallet : pluginOptions.mainwallet,
      pluginOptions.appName,
      pluginOptions.testnet,
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
      "wookey",
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
<main id="wookey-checkout" class="wookey-app wookey-app__grid">
  {#if pluginOptions.order.status !== "completed"}
  
    <h3>{pluginOptions.translations.payInviteTitle}</h3>
    <p>{pluginOptions.translations.payInviteText}</p>
    {#if protonCheckoutState.session}
    <a class="woow-button checkout-button button alt wc-forward wp-element-button" on:click={()=>protonCheckoutState.appState = APP_STATE_TOKEN_SELECT}>Pay as {protonCheckoutState.session.auth.actor.toString()}</a>
    {:else}
    <a class="woow-button checkout-button button alt wc-forward wp-element-button" on:click={()=>connectProton()}>{pluginOptions.translations.payInviteButtonLabel}</a>
    {/if}
  
  {:else}
    <h3>{pluginOptions.translations.orderStatusTitle}</h3>
    <p>{pluginOptions.translations.orderStatusText}</p>
  
  {/if}
  {#if protonCheckoutState.isRunning}
    <Dialog classes="select__token__dialog__content" open={protonCheckoutState.appState == APP_STATE_TOKEN_SELECT}>
      <div slot="head">
        <h3>{pluginOptions.translations.selectTokenDialogTitle}</h3>
        <p>{pluginOptions.translations.selectTokenDialogText}</p>
      </div>
      <div slot="content">
        <PayTokenSelector 
        storeCurrency={pluginOptions.wooCurrency} 
        cartAmount={pluginOptions.order.total.toString()} 
        changeSession={changeAccount} 
        selectPayToken={(token,amount)=>initTransfer(token,amount)} 
        allowedTokens={pluginOptions.allowedTokens} 
        actorName={protonCheckoutState.session.auth.actor.toString()}
        baseDomain={pluginOptions.baseDomain}
        translations={{
          processingLabel:pluginOptions.translations.selectTokenPayProcessingLabel,
          payLabel:pluginOptions.translations.selectTokenPayButtonLabel,
          connectedAdLabel:pluginOptions.translations.selectTokenDialogConnectedAs,
          changeAccountLabel:pluginOptions.translations.selectTokenDialogChangeAccountLabel
        }}
        />

      </div>
    </Dialog>
    <Dialog open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION}>
      <div slot="head">
        <h3>{pluginOptions.translations.verifyPaymentDialogTitle}</h3>
      </div>
      <div slot="content">
        <PaymentVerify 
        network={pluginOptions.testnet?'testnet':'mainnet'}
        paymentKey={pluginOptions.paymentKey} 
        transactionId={protonCheckoutState.tx.processed.id} 
        onVerify={onPaymentVerify}
        baseDomain={pluginOptions.baseDomain}
        translations={{
          processingLabel:pluginOptions.translations.verifyPaymentDialogProcessLabel,
          verifyText:pluginOptions.translations.verifyPaymentDialogText
        }}
        ></PaymentVerify>
      </div>
    </Dialog>
    <Dialog footerClose open={protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_SUCCESS}>
      <div slot="head">
        <h3>{pluginOptions.translations.verifySuccessPaymentDialogTitle}</h3>
      </div>
      <div slot="content">
        <PaymentSucceed translations={{text:pluginOptions.translations.verifySuccessPaymentDialogText}}></PaymentSucceed>
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
