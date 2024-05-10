<script lang="ts">
  import './../../../styles/base.scss';
  import './../../../styles/refund.scss';
  import { onMount } from 'svelte';
  import Dialog from '../../commons/components/dialogs/Dialog.svelte'
  import {APP_STATE_STORE_REGISTRATION, APP_STATE_STORE_REGISTRATION_SUCCEED} from './constants';
  import { MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS} from '../../commons/constants';
  import { generateRefundAction } from '../../commons/proton/actions/refund';
  import type { RefundControllerOption, RefundState } from './refund.type';
  import { canRestoreSession } from '../../commons/utils';
  import { webauthConnect } from '../../commons/proton/connect';
  import { getPayment } from '../../commons/services/Payments';
  import Processing from '../../commons/components/processing/Processing.svelte';
  import StatusChip from '../../commons/components/payment/StatusChip.svelte';
  
  
  let payment = null
  let pluginOptions: RefundControllerOption = window['wookeyRefundParams'] as RefundControllerOption;
  let controllerState:RefundState = {
    isRunning:false,
    session:null,
    appState:'',
    

  }
  /*
  */

  onMount(async ()=>{

    if(canRestoreSession()) connectProton(true);
    console.log(pluginOptions)
    refreshData();
    
  })

  async function refreshData (){
    payment = await getPayment(pluginOptions.order.paymentKey,pluginOptions.testnet)
  }

  async function  connectProton(restoreSession = false) {
    
    const  session = await webauthConnect(
      pluginOptions.testnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor,
      'Wookey',
      pluginOptions.testnet,
      restoreSession
      )
    controllerState.isRunning = !!session
    if (session) {
      controllerState.session = session;
    }
    return session

  }


  async function refundPayment (paymentKey:string){

    const refundActions = generateRefundAction(controllerState.session.auth.actor.toString(),controllerState.session.auth.permission.toString(),paymentKey);
    console.log(refundActions)
    if (controllerState.session){
      const tx = await controllerState.session.transact({
        actions:[refundActions]
      },{
        broadcast:true
      })
    }
    refreshData();
  }

  

</script>
<main class='wookey-app wookey-app__grid  wookey-refund'>
  {#if !payment}
  <Processing label="Fetching payment"></Processing>
  {:else}
    <ul class="wookey-refund__order-info">
      <li class="wookey-refund__order-info__render-item">
        <h4>On chain status</h4>
        <StatusChip variant='large' status={payment.status}></StatusChip>
      </li>
      <li class="wookey-refund__order-info__render-item">
        <h4>Token amount</h4>
        <p>{payment.amount}</p>
      </li>
      <li class="wookey-refund__order-info__render-item">
        <h4>Network</h4>
        <p>{pluginOptions.network}</p>
      </li>
      <li class="wookey-refund__order-info__render-item">
        <h4>Payment Key</h4>
        <a class="button" target="_blank" href={pluginOptions.testnet ? `https://testnet.explorer.xprnetwork.org/account/wookey?loadContract=true&tab=Tables&table=payments&account=wookey&scope=wookey&limit=100&lower_bound=${payment.key}&upper_bound=${payment.key}`: `https://explorer.xprnetwork.org/account/wookey?loadContract=true&tab=Tables&table=payments&account=wookey&scope=wookey&limit=100&lower_bound=${payment.key}&upper_bound=${payment.key}`}>{pluginOptions.order.paymentKey}</a>
      </li>
      <li class="wookey-refund__order-info__render-item">
        <h4>Transaction Id</h4>
        <a class="button" target="_blank" href={pluginOptions.testnet ? `https://testnet.explorer.xprnetwork.org/transaction/${pluginOptions.order.transactionId}`: `https://explorer.xprnetwork.org/transaction/${pluginOptions.order.transactionId}`}>{pluginOptions.order.transactionId}</a>
      </li>
      
    </ul>
  
 
  
  <footer class="wookey-refund__refund-option">
    {#if payment.status == -2 && pluginOptions.order.status != 'refunded'}

        <div class="wookey-refund__refund-option__warning">
          <h4>Order status mismatch</h4> 
          <p>This order has been refunded on-chain, but still marked as {pluginOptions.order.status} in WooCommerce. Please change status manually to <b>"Refunded"</b> in <b>"Order #{pluginOptions.order.id} details"</b>  when done.</p> 
        </div>

      {/if}
    {#if controllerState.session}
    <button disabled={payment.status != 1 } on:click|preventDefault={()=>{refundPayment(pluginOptions.order.paymentKey)}} class="woow-button button-primary full-width">Refund from {pluginOptions.testnetActor}</button>
    {:else }
    <button on:click|preventDefault={()=>{connectProton(false)}} class="woow-button button-primary full-width">Connect WebAuth to refund</button>
    {/if}
  </footer>
  {/if}  
</main>