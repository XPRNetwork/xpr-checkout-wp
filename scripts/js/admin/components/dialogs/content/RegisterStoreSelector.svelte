<script lang="ts">
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
      <button class="register_store__button" on:click|preventDefault={()=>onRegister()}>
        <h5 class="register_store__button__label">
          Register {actorName} on {isTestnet ? 'testnet' : 'mainnet'}
        </h5>
        <div class="register_store__button__drill_icon">
          <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.1584 3.13508C6.35985 2.94621 6.67627 2.95642 6.86514 3.15788L10.6151 7.15788C10.7954 7.3502 10.7954 7.64949 10.6151 7.84182L6.86514 11.8418C6.67627 12.0433 6.35985 12.0535 6.1584 11.8646C5.95694 11.6757 5.94673 11.3593 6.1356 11.1579L9.565 7.49985L6.1356 3.84182C5.94673 3.64036 5.95694 3.32394 6.1584 3.13508Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
        </div>
      </button>
  <div>Connect as <b>@{actorName}</b>, <a on:click|preventDefault={changeSession} href="#">change account ?</a></div>
</div>
<style>

  .register_store{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;

  }
  .register_store__list{

    display: grid;
    grid-template-columns: 1fr;
    gap:10px;
    list-style: none;
    padding: 0;
    margin: 0;

  }

  .register_store__button {

    display: grid!important;
    grid-template-columns: 1fr min-content;
    padding: 10px!important;
    margin: 0!important;
    align-items: center;
    
  }

  .register_store__button:hover {

    background-color: black!important;

  }

  .register_store__button__drill_icon {

    

  }
  .register_store__button__label {

    text-align: left;
    

  }
</style>