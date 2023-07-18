<script lang="ts">
  import {truncateToPrecision} from './utils/price'
  import axios, { type AxiosRequestConfig } from 'axios';
  import {onMount} from 'svelte';
  import type { TokenRate } from './App.svelte';

  export let allowedTokens:string;
  export let actorName:string;
  export let cartAmount:string;
  export let changeSession = ()=>{
    console.log('change session')
  }
  
  export let selectPayToken = (token:TokenRate,amount:number)=>{
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
      <button class="token_rates__list__render_item" on:click={()=>selectPayToken(getTokenRateBySymbol(token.symbol),convertFiatPriceToToken(parseFloat(cartAmount),token.symbol))}>
        <h5 class="token_rates__list__render_item__token_price">
          Pay {convertFiatPriceToToken(parseFloat(cartAmount),token.symbol).toLocaleString()} {token.symbol}
        </h5>
        <div class="token_rates__list__render_item__drill_icon">

        </div>
      </button>
    </li>  
    {/each}
  </ul>
  {/if}
</div>
<div>Connect as <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">change account ?</a></div>
<style>

  .token_rates{}
  .token_rates__list{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;
    list-style: none;
    padding: 0;
    margin: 0;

  }

  .token_rates__list__render_item {

    display: grid;
    grid-template-columns: 1fr min-content;
    padding: 10px;
    border: 1px solid;
    width: 100%;

  }

  .token_rates__list__render_item:hover {

    background-color: blueviolet;

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