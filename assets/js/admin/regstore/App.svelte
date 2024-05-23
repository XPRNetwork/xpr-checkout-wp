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
  import './../../../styles/base.scss'
  import { onMount } from 'svelte';
  import ProtonWeb, { type LinkSession, type TransactResult } from '@proton/web-sdk';
  import Dialog from '../../commons/components/dialogs/Dialog.svelte'
  import {APP_STATE_STORE_REGISTER, APP_STATE_STORE_REGISTRATION_SUCCEED, APP_STATE_STORE_UNREGISTER} from './constants';
  import RegisterStore from '../../commons/components/dialogs/content/RegisterStoreSelector.svelte';
  import { generateRegisterStoreAction } from '../../commons/proton/actions/registerStore';
  import { webauthConnect } from '../../commons/proton/connect';
  import { isStoreRegistered } from '../../commons/services/checkStore';
  import { canRestoreSession } from '../../commons/utils';
  import UnregisterStoreSelector from '../../commons/components/dialogs/content/UnregisterStoreSelector.svelte';
  import { generateUnregisterStoreAction } from '../../commons/proton/actions/unregisterStore';
  
  
  let storeRegistered:boolean = false;
  let isTestnet:boolean = false;
  let hasMainnetAccount:boolean = false;
  let hasTestnetAccount:boolean = false;
  let pluginOptions: ControllerOptions = window['xprcheckoutRegStoreParams'] as ControllerOptions;
  let mainnetAccountNameField = null;
  let testnetAccountNameField = null;
  let currentAccount = '';
  let controllerState = {
    isRunning:false,
    session:null,
    appState:'',
    tx:null

  }
  /*
  */

  onMount(async ()=>{

    console.log(pluginOptions,'pluginOptions')
    const networkCheckbox:HTMLSelectElement = document.querySelector(pluginOptions.networkSelector);
    mainnetAccountNameField = document.querySelector(pluginOptions.mainnetAccountFieldSelector);
    testnetAccountNameField = document.querySelector(pluginOptions.testnetAccountFieldSelector);
    
    isTestnet = networkCheckbox.value == "testnet";
    hasMainnetAccount = !isTestnet && pluginOptions.mainnetActor && pluginOptions.mainnetActor !== '' 
    hasTestnetAccount = isTestnet && pluginOptions.testnetActor && pluginOptions.testnetActor !== '' 
    currentAccount = isTestnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor
    if (networkCheckbox){
      networkCheckbox.addEventListener('change',async (e)=>{
        isTestnet = networkCheckbox.value == "testnet";
        hasMainnetAccount = !isTestnet && pluginOptions.mainnetActor && pluginOptions.mainnetActor !== '' 
        hasTestnetAccount = isTestnet && pluginOptions.testnetActor && pluginOptions.testnetActor !== '' 
        storeRegistered = await isStoreRegistered(isTestnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor,isTestnet)
        currentAccount = isTestnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor
      })
      storeRegistered = await isStoreRegistered(isTestnet ? pluginOptions.testnetActor : pluginOptions.mainnetActor,isTestnet)
   }

  })

  async function registerStore(){}

  async function  connectProton(restoreSession = false) {
    
    const session = await webauthConnect('wookey','XPRCheckout',isTestnet,restoreSession)
    console.log('connectProton',session,restoreSession)
    controllerState.isRunning = !!session
    if (session) {
      controllerState.session = session
      controllerState.appState = storeRegistered ? APP_STATE_STORE_UNREGISTER :APP_STATE_STORE_REGISTER;
    }
    return session

  }

  async function onStoreUnregister(){

    controllerState.appState = "";
    const registerAction = generateUnregisterStoreAction(controllerState.session.auth.actor,controllerState.session.auth.permission)
    console.log(registerAction);
    const tx:TransactResult = await controllerState.session.transact(
      {
        actions:[registerAction]
      },
      {
        broadcast:true
      }
    )

    if (isTestnet){

      testnetAccountNameField.value = ''

    }else {

      mainnetAccountNameField.value = ''

    }

    currentAccount = '';
    storeRegistered = false;
    controllerState.tx = tx;
    controllerState.appState = APP_STATE_STORE_REGISTRATION_SUCCEED;
    controllerState.isRunning = true;

  }
  async function onStoreRegister(){

    console.log('register store')

    controllerState.appState = "";
    const registerAction = generateRegisterStoreAction(controllerState.session.auth.actor,controllerState.session.auth.permission)
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
    storeRegistered = true;
    currentAccount = controllerState.session.auth.actor

  }

</script>
<main class='xprcheckout_register_store xprcheckout-app'>
  <Dialog open={controllerState.appState == APP_STATE_STORE_REGISTER}>
    <div slot="head">
      <h3 class="modal_title">Register your Store</h3>
    </div>
    <div slot="content">
      <RegisterStore changeSession={()=>connectProton(false)} isTestnet={isTestnet} actorName={controllerState.session.auth.actor} onRegister={onStoreRegister}></RegisterStore>
    </div>
  </Dialog>
  <Dialog open={controllerState.appState == APP_STATE_STORE_UNREGISTER}>
    <div slot="head">
      <h3 class="modal_title">Unregister {currentAccount}</h3>
    </div>
    <div slot="content">
      <UnregisterStoreSelector changeSession={()=>connectProton(false)} isTestnet={isTestnet} actorName={controllerState.session.auth.actor} onUnregister={onStoreUnregister}/>
    </div>
  </Dialog>
  
  {#if storeRegistered}
  <button class="xprcheckout-button button-primary" on:click|preventDefault={()=>connectProton(canRestoreSession())}>Unregister <b>{currentAccount}</b> on {isTestnet ? 'testnet' : 'mainnet'}</button>
  {:else}
  <button class="xprcheckout-button button-primary" on:click|preventDefault={()=>connectProton(canRestoreSession())}>Register <b>{currentAccount}</b> on {isTestnet ? 'testnet' : 'mainnet'}</button>
  {/if}
</main>

<style>


</style>