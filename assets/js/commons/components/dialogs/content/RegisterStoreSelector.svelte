<script lang="ts">
  import './../../../../../styles/components/button.scss'
  import {onMount} from 'svelte';
  export let actorName:string;
  export let isTestnet:boolean;
  export let changeSession = ()=>{
    console.log('change session')
  }
  
  export let onRegister = ()=>{
    console.log('register store')
  }

  onMount(async ()=>{

    
  })

</script>

<div class="register_store">
    <p>You are about to register your <b>{actorName}</b>'s' account as store account nearby the Woow escrow smart contract</p>
      <button class="woow-button button-primary full-width" on:click|preventDefault={()=>onRegister()}>Register {actorName} on {isTestnet ? 'testnet' : 'mainnet'}</button>
  <div>Connect as <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">change account ?</a></div>
</div>
<style>

  .register_store{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;

  }
  
</style>