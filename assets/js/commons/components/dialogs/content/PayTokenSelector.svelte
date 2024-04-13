<script lang="ts">
  import {truncateToPrecision} from '../../../utils/price'
  import {getUserBalanceForToken} from '../../../utils/tokens'
  import {getConvertedToUSD} from '../../../services/PriceRate'
  import {onMount} from 'svelte';
  import type { TokenRate } from '../../../type';
  import { getTokensPrices } from '../../../services/TokensPrices';
  import {getUserBalances} from '../../../services/UserBalance'
  import Processing from '../../processing/Processing.svelte';

  export let isTestnet:boolean;
  export let allowedTokens:string;
  export let actorName:string;
  export let cartAmount:string;
  export let storeCurrency:string;
  export let baseDomain:string = ""
  export let translations:any = {
    payLabel:"",
    connectedAdLabel:"",
    changeAccountLabel:"",
    processingLabel:""
  }
  let usdCartAmount:number;
  export let changeSession = ()=>{
    console.log('change session')
  }
  export let selectPayToken = (token:TokenRate,amount:number | string)=>{
    console.log('selected token')
  }

  const allowedTokensArray = allowedTokens.split(',');
  let allowedTokenRates:TokenRate[] = [];
  let refreshing = true

  onMount(async ()=>{
    
    refresh();

  })

  async function refresh (){

    console.log(isTestnet,'isTestnet,')
    refreshing = true
    const userBalances  = await getUserBalances(actorName,isTestnet);
    console.log(userBalances)
    const convertedOrderAmountAsUsd  = await getConvertedToUSD(baseDomain,storeCurrency,parseFloat(cartAmount));
    if (convertedOrderAmountAsUsd.status == 200){
      usdCartAmount = convertedOrderAmountAsUsd.data.body_response
    }
    const tokenRatesResult = await getTokensPrices(baseDomain);
    if (tokenRatesResult.status == 200){
      allowedTokenRates = tokenRatesResult.data.body_response.reduce((prev:TokenRate[],current:TokenRate):TokenRate[]=>{
        if(allowedTokensArray.some((token)=>token == current.symbol)){
          
          prev.push(current);
        }
        return prev;
      },[])
      allowedTokenRates = allowedTokenRates.map((tokenRate)=>{

        const rate = {...tokenRate}
        const availableUserBalance = getUserBalanceForToken(rate.pair_base,userBalances)

        if (availableUserBalance){

          rate.availableUserBalance = parseFloat(availableUserBalance.amount)
          rate.enable = parseFloat(availableUserBalance.amount)>=convertFiatPriceToToken(usdCartAmount,rate.pair_base)
          console.log(tokenRate.pair_base,usdCartAmount,parseFloat(availableUserBalance.amount),convertFiatPriceToToken(usdCartAmount,rate.pair_base))
          

        }else {
          rate.enable = false;
          rate.availableUserBalance = 0
          
        }
        return rate;
      })
      allowedTokenRates = allowedTokenRates.sort(orderTokensRate);
      refreshing = false
    }

  }

  function convertFiatPriceToToken (fiatAmount:number,symbol:string){
    const rate = getTokenRateBySymbol(symbol)
    if (rate){
      return parseFloat(truncateToPrecision(fiatAmount/rate.quote.price_usd,rate.decimals));
    }
    return 0
  }

  function getTokenRateBySymbol (symbol:string){
    return allowedTokenRates.find((token)=>token.symbol == symbol);
  }

  function orderTokensRate (a:TokenRate,b:TokenRate) {

    console.log('order',a,b)
    return +b.enable - +a.enable

  }

  
</script>


  {#if usdCartAmount}
  <div class="usd_cart_amount">
    <span><b>{cartAmount} {storeCurrency} = {truncateToPrecision(usdCartAmount,2)} USD</b></span>
    <a on:click={()=>refresh()}>Refresh </a>
  </div>
  {/if}
  {#if refreshing}
  <Processing label={translations.processingLabel}></Processing>
  {:else}
  <div style="overflow-y:auto; max-height:100%">
    <ul class="token_rates__list">
      {#each allowedTokenRates as token }
      <li >
        <a aria-roledescription="Select token" class={`${token.enable ? '' : 'disabled'} woow-button button  token_rates__list__render_item`} on:click={()=>selectPayToken(getTokenRateBySymbol(token.symbol),convertFiatPriceToToken(usdCartAmount,token.symbol))}>
          <img src={token.logo} />
          <div>
            <h5 class="token_rates__list__render_item__token_price">
              {translations.payLabel} {convertFiatPriceToToken(usdCartAmount,token.symbol)} {token.symbol}
            </h5>
            <p class="token_rates__list__render_item__token_balance">
              Balance 
              {token.availableUserBalance} 
              {token.symbol}
            </p>
          </div>
          
          <div class="token_rates__list__render_item__drill_icon">
            <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.1584 3.13508C6.35985 2.94621 6.67627 2.95642 6.86514 3.15788L10.6151 7.15788C10.7954 7.3502 10.7954 7.64949 10.6151 7.84182L6.86514 11.8418C6.67627 12.0433 6.35985 12.0535 6.1584 11.8646C5.95694 11.6757 5.94673 11.3593 6.1356 11.1579L9.565 7.49985L6.1356 3.84182C5.94673 3.64036 5.95694 3.32394 6.1584 3.13508Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
          </div>
        </a>
      </li>  
      {/each}
    </ul>
  </div>
  
  {/if}
  <div>{translations.connectedAdLabel} <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">{translations.changeAccountLabel}</a></div>

<style>

  .usd_cart_amount {

    display: flex;
    justify-content: space-between;

  }

  .token_rates{

    display: grid;
    grid-template-columns: 1fr;
    grid-template-rows: min-content 1fr min-content;
    gap:10px;

  }
  .token_rates__list{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;
    list-style: none;
    padding: 0;
    margin: 0;

  }

  .token_rates__list__render_item {

    display: grid!important;
    grid-template-columns: 40px 1fr min-content;
    padding: 10px!important;
    margin: 0!important;
    align-items: center;
    gap:10px;
    
  }
  
  .token_rates__list__render_item.disabled {

    pointer-events: none;
    opacity: 0.3;
    
  }
  
  .token_rates__list__render_item.disabled  .token_rates__list__render_item__drill_icon {

    display: none;
    
  }


  .token_rates__list__render_item__drill_icon {

    width: 16px;
    height: 30px;
    

  }
  .token_rates__list__render_item__token_price {

    text-align: left;
    font-weight: 700;
    padding: 0;
    margin: 0;
    text-transform: none;

  }
  .token_rates__list__render_item__token_balance {

    text-align: left;
    font-size: 12px;
    padding: 0;
    margin: 0;
    text-transform: none;
    text-decoration: none!important;

  }
</style>