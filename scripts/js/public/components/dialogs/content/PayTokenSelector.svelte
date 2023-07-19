<script lang="ts">
  import {truncateToPrecision} from '../../../utils/price'
  import axios, { type AxiosRequestConfig } from 'axios';
  import {onMount} from 'svelte';
  import type { TokenRate } from './../../../type';

  export let allowedTokens:string;
  export let actorName:string;
  export let cartAmount:string;
  export let changeSession = ()=>{
    console.log('change session')
  }
  
  export let selectPayToken = (token:TokenRate,amount:number | string)=>{
    console.log('selected token')
  }
  
  const allowedTokensArray = allowedTokens.split(',');
  let allowedTokenRates:TokenRate[] = [];

  onMount(async ()=>{

    console.log(cartAmount,'cartAmount')
    const tokenRatesRequest:AxiosRequestConfig = {url:'https://proton.alcor.exchange/api/v2/tokens'}
    const tokenRatesResult = await axios<TokenRate[]>(tokenRatesRequest);
    if (tokenRatesResult.status == 200){
      allowedTokenRates = tokenRatesResult.data.reduce((prev:TokenRate[],current)=>{

        if(allowedTokensArray.some((token)=>token == current.symbol)){
          prev.push(current);
        }
        return prev;

      },[]);
      console.log(allowedTokenRates)
    }
  })

  function convertFiatPriceToToken (fiatAmount:number,symbol:string){

    const rate = getTokenRateBySymbol(symbol)
    if (rate){

      console.log (truncateToPrecision(fiatAmount/rate.usd_price,rate.decimals),'truncateToPrecision(fiatAmount/rate.usd_price,rate.decimals)')
      return truncateToPrecision(fiatAmount/rate.usd_price,rate.decimals)

    }
    return 0

    

  }

  function getTokenRateBySymbol (symbol:string){

    return allowedTokenRates.find((token)=>token.symbol == symbol);
  }

  
</script>

<div class="token_rates">
  {#if allowedTokenRates.length == 0}
    <p>Fetch token rate</p>
  {:else}
  <ul class="token_rates__list">
    {#each allowedTokenRates as token }
    <li >
      <a aria-roledescription="Select token" class="checkout-button button alt wc-forward wp-element-button token_rates__list__render_item" on:click={()=>selectPayToken(getTokenRateBySymbol(token.symbol),convertFiatPriceToToken(parseFloat(cartAmount),token.symbol))}>
        <h5 class="token_rates__list__render_item__token_price">
          Pay {convertFiatPriceToToken(parseFloat(cartAmount),token.symbol).toLocaleString()} {token.symbol}
        </h5>
        <div class="token_rates__list__render_item__drill_icon">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.1584 3.13508C6.35985 2.94621 6.67627 2.95642 6.86514 3.15788L10.6151 7.15788C10.7954 7.3502 10.7954 7.64949 10.6151 7.84182L6.86514 11.8418C6.67627 12.0433 6.35985 12.0535 6.1584 11.8646C5.95694 11.6757 5.94673 11.3593 6.1356 11.1579L9.565 7.49985L6.1356 3.84182C5.94673 3.64036 5.95694 3.32394 6.1584 3.13508Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </div>
      </a>
    </li>  
    {/each}
  </ul>
  {/if}
  <div>Connect as <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">change account ?</a></div>
</div>
<style>

  .token_rates{

    display: grid;
    grid-template-columns: 1fr;
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
    grid-template-columns: 1fr min-content;
    padding: 10px!important;
    margin: 0!important;
    
  }

  .token_rates__list__render_item:hover {

    background-color: black!important;

  }

  .token_rates__list__render_item__drill_icon {

    width: 16px;
    height: 30px;
    background-image: url('../../public/img/drill_icon.png');
    background-position: center center;
    background-size: contain;
    background-repeat: no-repeat;

  }
  .token_rates__list__render_item__token_price {

    text-align: left;
    font-weight: 700;
    padding: 0;
    margin: 0;

  }
</style>