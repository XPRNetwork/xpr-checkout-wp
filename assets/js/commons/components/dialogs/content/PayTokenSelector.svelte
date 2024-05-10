<script lang="ts">
  import {toPrecision} from '../../../utils/price'
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

  export async function refresh (){

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
      console.log(fiatAmount,rate.quote.price_usd,rate.decimals)
      return parseFloat(toPrecision(fiatAmount/rate.quote.price_usd,rate.decimals));
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

  <div class="flex flex-col gap-4">

  
  {#if usdCartAmount}
  <div class="usd_cart_amount">
    <p class="text-sm font-bold text-gray-500">{cartAmount} {storeCurrency} = {toPrecision(usdCartAmount,2)} USD</p>
    <a class="text-sm font-bold text-gray-500" on:click={()=>refresh()}>Refresh </a>
  </div>
  {/if}
  {#if refreshing}
  <Processing label={translations.processingLabel}></Processing>
  {:else}
  <div style="">
    <p class="font-bold text-lg">Select the token you want to pay with</p>
    <ul class="grid grid-cols-1 gap-2">
      {#each allowedTokenRates as token }
      <li class="card hover:bg-white shadow-sm rounded-md hover:shadow-lg hover:z-20 border-2 border-gray-50 hover:border-primary p-4 " >
        <a  aria-roledescription="Select token" class={`grid grid-cols-[40px,1fr,min-content] items-center gap-4 ${token.enable ? '' : 'disabled'}`} on:click={()=>selectPayToken(getTokenRateBySymbol(token.symbol),convertFiatPriceToToken(usdCartAmount,token.symbol))}>
          <img width="45"  class="max-w-fit" src={token.logo} />
          <div>
            <p class="font-bold token_rates__list__render_item__token_price">
              {translations.payLabel} {convertFiatPriceToToken(usdCartAmount,token.symbol)} {token.symbol}
            </p>
            <p class="token_rates__list__render_item__token_balance">
              Balance 
              {token.availableUserBalance} 
              {token.symbol}
            </p>
          </div>
          
          <div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#" class="w-6 h-6">
              <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 0 1 0 1.06l-7.5 7.5a.75.75 0 0 1-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 0 1 1.06-1.06l7.5 7.5Z" clip-rule="evenodd" />
            </svg>
            
          </div>
        </a>
      </li>  
      {/each}
    </ul>
  </div>
  
  {/if}
</div>

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

  .token_rates__list__render_item__token_logo{
    max-width: 100%;
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