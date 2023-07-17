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
  import {MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS, WOO_CHECKOUT_FORM_SELECTOR} from './constants';
  
  
  let protonSession:LinkSession | undefined  = undefined;
  let isTestnet:boolean = false;

  onMount(()=>{

    const testnetCheckbox = document.querySelector('#woocommerce_wagateway_testnet');
    testnetCheckbox.addEventListener('change',(e)=>{

      console.log(e.target)

    })

  })

  async function  connectProton() {
    
    
    const { session, link } = await ProtonWeb({
      linkOptions: {
        chainId: isTestnet ? TESTNET_CHAIN_ID : MAINNET_CHAIN_ID,
        endpoints: isTestnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS,
      },
      transportOptions: {
        requestAccount: 'wowa', 
      },
      selectorOptions: {
        appName: "Woo Webauth gateway",
      }
    })
    
    return session

  }

</script>

