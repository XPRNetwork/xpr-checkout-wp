<script lang="ts">
  import '../../../styles/base.scss'
  import '../../../styles/dashboard.scss'
  import {onMount} from 'svelte';
  import { getStoreBalance } from '../../commons/services/StoreBalances';
  import Processing from '../../commons/components/processing/Processing.svelte';
  import type { PayoutControllerOption, PayoutState } from './payout.type';
  import { generateWithdrawAction } from '../../commons/proton/actions/withdraw';
  import { getPayments } from '../../commons/services/Payments';
  import { getTokensPrices } from '../../commons/services/TokensPrices';
  import { getConvertedToUSD } from '../../commons/services/PriceRate';
  import { generateRefundAction } from '../../commons/proton/actions/refund';
  import {webauthConnect} from '../../commons/proton/connect';
  import {canRestoreSession} from '../../commons/utils/index'
  import StatusChip from '../../commons/components/payment/StatusChip.svelte';
  let balances:any[];
  let payments:any[];
  let tokenPrice:any;
  let priceRate:any;
  let refreshing:boolean = false;
  let pluginOptions: PayoutControllerOption = window['wookeyDashboardParams'] as PayoutControllerOption;
  
  let payoutState:PayoutState = {
    isRunning:false,
    session:null,
  }
  onMount(async ()=>{

    console.log(pluginOptions)
    refreshData();
    if (canRestoreSession()) connectProton(true,true)

  })

  async function refreshData (){

    refreshing = true;
    balances  =  await getStoreBalance(pluginOptions.testnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor,pluginOptions.testnet);
    payments = (await getPayments(pluginOptions.baseDomain,pluginOptions.testnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor,pluginOptions.testnet)).data;
    tokenPrice = (await getTokensPrices(pluginOptions.baseDomain)).data;
    priceRate = (await getConvertedToUSD(pluginOptions.baseDomain,pluginOptions.wooCurrency,1)).data;
    refreshing = false;
  
  }

  function getBalancesSymbol (){

    return balances.map((balance)=>{

      return balance.key

    })

  }

  async function connectProton(restoreSession = false,silentRestore=false) {
    
    if (payoutState.session)  {
        payoutState.isRunning = true
        return payoutState.session;
    };
    const session = await webauthConnect("wookey","Wookey",pluginOptions.testnet,restoreSession)
    payoutState.isRunning = !!session
    if (session) {
      payoutState.session = session
      
    }
    console.log(session)
    return session

  }

  function getSymbolCode (symbol:string){

    //TODO: Unsafe
    return symbol.split(',')[1]

  }
  function getSymbolFormAmount (symbol:string){

    //TODO: Unsafe
    return symbol.split(' ')[1]

  }
  
  function getSymbolLogo (symbol:string){

    //TODO: Unsafe
    const currentToken = tokenPrice.body_response.find((token)=>token.pair_base == symbol);
    return currentToken.logo

  }

  function getCurrencyPrice (symbol:string,amount:number){

    const currentToken = tokenPrice.body_response.find((token)=>token.pair_base == symbol);
    if (currentToken){
      return `${(amount*(currentToken.quote.price_usd/priceRate.body_response)).toFixed(2)} ${pluginOptions.wooCurrency}`;
    }
    return '--'

  }

  async function withdraw (symbols:number[]){

    const actions =  symbols.map((symbol)=>generateWithdrawAction(
      payoutState.session.auth.actor.toString(),payoutState.session.auth.permission.toString(),symbol))
    if (payoutState.session){
      const tx = await payoutState.session.transact({
        actions:actions
      },{
        broadcast:true
      })

      refreshData();

    }

  }
  
  

</script>
<main class='wookey-dashboard wookey-app'>
  <div class="wookey-dashboard__header">
    <div class="wookey-dashboard__title">
      <h3>Wookey Dashboard</h3>
    </div>
    <div class="wookey-dashboard__actions">
      
    </div>
  </div>
  <div class="wookey-dashboard__body">
    <div class="wookey-dashboard__payments">
      {#if !refreshing && balances} 
    <table class="wp-list-table widefat fixed striped table-view-list posts">
      <thead>
        <td>Order</td>
        <td>Status</td>
        <td>Amount</td>
      </thead>
      <tbody>
      {#each payments as payment } 
      <tr>
        <td><a href={`${pluginOptions.baseDomain}/wp-admin/post.php?post=${payment.id}&action=edit`}><b>#{payment.id} - {payment.buyer}</b></a></td>
        <td>
          <StatusChip status={payment.status}></StatusChip>
        </td>
        <td>
          <div class="payments__list__amount">
            <p><b>{payment.amount}</b></p>
            <p>{getCurrencyPrice(getSymbolFormAmount(payment.amount),parseFloat(payment.amount))}</p>
          </div>
        </td>
        
      </tr>
      {/each}
      </tbody>
    </table>
    {/if}
    
    </div>
    <div class="wookey-dashboard__withdraws">
    {#if !refreshing && balances}
      <ul class="wookey-dashboard__withdraws__balances">
        {#each balances as balance }
        <li class="wookey-dashboard__withdraws__balances__render-item"> 
          <img width="40" src={getSymbolLogo(getSymbolCode(balance.key))}/>
          <div class="amounts">
            <span class="token">{balance.amount}</span>
            <h4>{getCurrencyPrice (getSymbolCode(balance.key),parseFloat(balance.amount))}</h4>
          </div>
        </li>  
        {/each} 
      </ul>
      
      <footer class="withdraw-all">
        {#if payoutState.session}
        <button on:click={()=>withdraw(getBalancesSymbol())} class="woow-button button-primary full-width">Withdraw as {pluginOptions.testnetActor}</button>
        {:else }
        <button on:click={()=>connectProton()} class="woow-button button-primary full-width">Connect WebAuth to withdraw</button>
        {/if}
      </footer>
      {/if}
    
    </div>
  </div>
  
  {#if refreshing}
    <Processing label={`Fetching balances for`}></Processing>
  {/if}
  <div class="withdaw-app">

  
  <div class="payments__list">

    
  </div>
  <div>
  
  
</div>
</div>
</main>

<style lang="scss">

  .balances__list {

    display: grid;
    border: 1px solid #c3c4c7;
   
  }
  

  .withdraw-all {

    padding: 10px;
    background-color: #ffffff;

  }

  

</style>