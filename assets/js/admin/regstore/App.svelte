<script lang="ts" context="module">
 interface ControllerOptions {
  networkCheckBoxSelector:string;
  testnetAccountFieldSelector:string;
  mainnetAccountFieldSelector:string;
  mainnetActor:string;
  testnetActor:string;  


 }
</script>
<script lang="ts">
  import './../../../styles/main.scss'
  import { onMount } from 'svelte';
  import ProtonWeb, { type LinkSession, type TransactResult } from '@proton/web-sdk';
  import Dialog from '../../commons/components/dialogs/Dialog.svelte'
  import {APP_STATE_STORE_REGISTRATION, APP_STATE_STORE_REGISTRATION_SUCCEED} from './constants';
  import { MAINNET_CHAIN_ID, MAINNET_ENDPOINTS, TESTNET_CHAIN_ID, TESTNET_ENDPOINTS} from '../../commons/constants';
  import RegisterStore from '../../commons/components/dialogs/content/RegisterStoreSelector.svelte';
  import { generateRegisterStoreAction } from '../../commons/proton/actions/registerStore';
  
  
  let protonSession:LinkSession | undefined  = undefined;
  let isTestnet:boolean = false;
  let hasMainnetAccount:boolean = false;
  let hasTestnetAccount:boolean = false;
  let pluginOptions: ControllerOptions = window['woowRegStoreParams'] as ControllerOptions;
  let mainnetAccountNameField = null
  let testnetAccountNameField = null
  let controllerState = {
    isRunning:false,
    session:null,
    appState:'',
    tx:null

  }
  /*
  */

  onMount(()=>{

   const networkCheckbox = document.querySelector(pluginOptions.networkCheckBoxSelector);
   mainnetAccountNameField = document.querySelector(pluginOptions.mainnetAccountFieldSelector);
   testnetAccountNameField = document.querySelector(pluginOptions.testnetAccountFieldSelector);
   isTestnet = networkCheckbox.checked;
   hasMainnetAccount = !isTestnet && pluginOptions.mainnetActor && pluginOptions.mainnetActor !== '' 
   hasTestnetAccount = isTestnet && pluginOptions.testnetActor && pluginOptions.testnetActor !== '' 
   if (networkCheckbox){
    networkCheckbox.addEventListener('change',(e)=>{
      isTestnet = networkCheckbox.checked;
      hasMainnetAccount = !isTestnet && pluginOptions.mainnetActor && pluginOptions.mainnetActor !== '' 
      hasTestnetAccount = isTestnet && pluginOptions.testnetActor && pluginOptions.testnetActor !== '' 
    })
   }

  })

  async function registerStore(){}

  async function  connectProton() {
    
    const { session, link } = await ProtonWeb({
      linkOptions: {
        chainId: isTestnet ? TESTNET_CHAIN_ID : MAINNET_CHAIN_ID,
        endpoints: isTestnet ? TESTNET_ENDPOINTS : MAINNET_ENDPOINTS,
      },
      transportOptions: {
        requestAccount: 'woow', 
      },
      selectorOptions: {
        appName: "Woo Webauth gateway",
      }
    })
    controllerState.isRunning = !!session
    if (session) {
      controllerState.session = session
      controllerState.appState = APP_STATE_STORE_REGISTRATION
    }
    return session

  }

  async function onStoreRegister(){

    controllerState.appState = "";
    const registerAction = generateRegisterStoreAction(controllerState.session.auth.actor,controllerState.session.auth.permission)
    console.log(registerAction);
    const tx:TransactResult = await controllerState.session.transact(
      {
        actions:[registerAction]
      },
      {
        broadcast:true
      }
    )

    controllerState.tx = tx;
    controllerState.appState = APP_STATE_STORE_REGISTRATION_SUCCEED;
    controllerState.isRunning = true;
    if (isTestnet){

      testnetAccountNameField.value = controllerState.session.auth.actor

    }else {

      mainnetAccountNameField.value = controllerState.session.auth.actor

    }

  }

</script>
<main class='woow_regiter_store'>
  <Dialog open={controllerState.appState == APP_STATE_STORE_REGISTRATION}>
    <div slot="head">
      <h3 class="modal_title">Register your Store</h3>
    </div>
    <div slot="content">
      <RegisterStore isTestnet={isTestnet} actorName={controllerState.session.auth.actor} onRegister={onStoreRegister}></RegisterStore>
    </div>
  </Dialog>
  {#if hasMainnetAccount || hasTestnetAccount  }
  <button on:click|preventDefault={connectProton}>Register as <b>{isTestnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor}</b> on {isTestnet ? 'testnet' : 'mainnet'}</button>
  {:else}
  <button on:click|preventDefault={connectProton}>Connect Webauth on {isTestnet ? 'testnet' : 'mainnet'}</button>
  {/if}
</main>

<style>



  .woow_regiter_store h3 {

    padding: 0!important;
    margin: 0!important;

  }
</style>