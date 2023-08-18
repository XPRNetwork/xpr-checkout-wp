<script lang="ts">
  import './../../../../../styles/components/button.scss'
  import {onMount} from 'svelte';
  export let actorName:string;
  export let isTestnet:boolean;
  export let changeSession = ()=>{
    console.log('change session')
  }
  
  export let onUnregister = ()=>{
    console.log('register store')
  }

  onMount(async ()=>{

    
  })

</script>

<div class="register_store">
    <div class="warning">
      <p>You are about to unregister your <b>{actorName}</b>'s' account as store account nearby the Wookey escrow smart contract.</p>
      <p>Please be sure to withdraw all amount from the smart contract in order to change the registered account.</p>
    </div>
    
      <button class="woow-button button-primary full-width" on:click|preventDefault={()=>onUnregister()}>Unregister {actorName} on {isTestnet ? 'testnet' : 'mainnet'}</button>
  <div>Connect as <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">change account ?</a></div>
</div>
<style>

  .register_store{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;

  }
  
</style>