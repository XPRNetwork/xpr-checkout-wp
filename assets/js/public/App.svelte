<script lang="ts">
  import type {  TokenRate, PaymentVerifyResponse, WPResponse } from '../commons/type';
  import './../../styles/base.scss'
  import type { ConfigWithCart, ConfigWithOrder, Order, ProtonCheckOutState  } from './../commons/type';
  import { onMount } from 'svelte';
  import PayTokenSelector from '../commons/components/dialogs/content/PayTokenSelector.svelte';
  import PaymentSucceed from '../commons/components/dialogs/content/PaymentSucceed.svelte';
  import PaymentVerify from '../commons/components/dialogs/content/PaymentVerify.svelte';
  import {webauthConnect} from '../commons/proton/connect';
  import {generateTransferAction,generateRegisterPaymentAction} from '../commons/proton/actions/';
  import {APP_STATE_INVALID_ORDER, APP_STATE_PENDING_LOGIN, APP_STATE_TOKEN_SELECT, APP_STATE_TRANSFER_FAILURE, APP_STATE_TRANSFER_PROCESSING, APP_STATE_TRANSFER_VERIFICATION, APP_STATE_TRANSFER_VERIFICATION_FAILURE, APP_STATE_TRANSFER_VERIFICATION_SUCCESS} from './constants/';
  import {toPrecision,canRestoreSession} from '../commons/utils/index'
  
  import { updateOrder } from '../commons/services/UpdateOrder';
  import type { TransactResult } from '@proton/web-sdk';
  import PaymentFailure from '../commons/components/dialogs/content/PaymentFailure.svelte';
  import Processing from '../commons/components/processing/Processing.svelte';
  import InvalidOrder from '../commons/components/dialogs/content/InvalidOrder.svelte';
  
  let params: ConfigWithOrder = window['params'] as ConfigWithOrder;
  let protonCheckoutState:ProtonCheckOutState = {isRunning:false};
  
  
  onMount(async ()=>{

    console.log(params.order)
    if(canRestoreSession()){
      const session = await connectProton(true,true);
      if (session){
        protonCheckoutState.appState = computeStateOnOrder(params.order);
        return 
        
      }else {
        protonCheckoutState.appState = APP_STATE_PENDING_LOGIN;
        return 
      }
    }else {
      protonCheckoutState.appState = APP_STATE_PENDING_LOGIN; 
      return 
    }
    
  })

  async function connectProton(restoreSession = false,silentRestore=false) {
    
    console.log("connect proton")
    if (protonCheckoutState.session)  {
        return protonCheckoutState.session;
    };
    const session = await webauthConnect(
      params.testnet ? params.testnetActor : params.mainnetActor,
      params.appName,
      params.testnet.toString()=="1",
      restoreSession 
    )
    protonCheckoutState.isRunning = !!session
    if (session) {
      protonCheckoutState.session = session;
      
    }
    return session

  }

  function changeAccount (){

    protonCheckoutState.session = undefined;
    connectProton(false)

  }

  function onPaymentVerify (verifyResult:WPResponse<Order>){

    console.log(verifyResult,verifyResult.status)
    if(verifyResult && verifyResult.status==200){

      if (verifyResult.status == 200){
      params.order = verifyResult.body_response;
      protonCheckoutState.appState = computeStateOnOrder(params.order);

    }

  }
}

  async function initTransfer (token:TokenRate,amount:number | string){

    
    if (!protonCheckoutState || !protonCheckoutState.session) return;
    
    await updateOrder(params.baseDomain,params.order.paymentKey,token.pair_base);
    const registerPaymentAction = generateRegisterPaymentAction(
      params.testnet ? params.testnetActor : params.mainnetActor ,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      params.order.paymentKey,
      toPrecision(amount,token.decimals),
      token.symbol,
      token.contract
      
    )
    const transferAction = generateTransferAction(
      token.contract,
      protonCheckoutState.session.auth.actor.toString(),
      protonCheckoutState.session.auth.permission.toString(),
      "wookey",
      toPrecision(amount,token.decimals),
      token.symbol,
      params.order.paymentKey
    )

    try {
      protonCheckoutState.appState = APP_STATE_TRANSFER_PROCESSING
      const tx:TransactResult = await protonCheckoutState.session.transact(
      {
        actions:[
          registerPaymentAction,
          transferAction
        ]
      },
      {
        broadcast:true

      }
    )

    protonCheckoutState.tx = tx;
    protonCheckoutState.appState = APP_STATE_TRANSFER_VERIFICATION;
    

    }catch (e:any){

      console.log("Error occur")
      protonCheckoutState.appState = APP_STATE_TRANSFER_FAILURE
      
    } 
  }

  function computeStateOnOrder (order:Order):number{

    if (!order.paymentKey) return APP_STATE_INVALID_ORDER;
    if (order.paymentVerified && order.transactionId && order.status == "processing") return APP_STATE_TRANSFER_VERIFICATION_SUCCESS;
    if (order.paymentVerified && order.paymentKey && order.status == "pending") return APP_STATE_TRANSFER_VERIFICATION;
    if (!order.paymentVerified && order.paymentKey && !order.transactionId && order.status == "pending") return APP_STATE_TOKEN_SELECT;
    return APP_STATE_PENDING_LOGIN;

  }

  function setAppState(appState:number){

    protonCheckoutState.appState = appState;

  }

</script>

<main class=" flex flex-col px-4 gap-1 min-h-screen" data-theme="acidex">
  <div class="flex justify-between items-center my-8">
    <div class="flex gap-4 items-center">
      <div class="brand__icon flex-shrink-0"></div>
      <div class="flex flex-col">
        <p class="text-3xl font-extrabold uppercase text-primary">Pay With WebAuth</p>
        {#if !!protonCheckoutState.session}
        <p class="text-sm">{params.translations.selectTokenDialogConnectedAs} <b>@{protonCheckoutState.session.auth.actor.toString()}</b>, <a on:click|preventDefault={changeAccount} href="#">{params.translations.selectTokenDialogChangeAccountLabel}</a></p>
        {/if}
      </div>
    </div>
    <a class={`btn btn-sm btn-outline rounded-md ${protonCheckoutState.appState > APP_STATE_TRANSFER_PROCESSING ? 'btn-disabled' : ''}`}  href={params.order.cancelRedirect}>Cancel</a>
  </div>
  
  <div class="bg-white p-2 md:p-8">
  <ol class="grid grid-cols-[1fr,1fr,1fr,max-content] items-center w-full text-sm text-gray-500 font-medium sm:text-base">
    <li class="flex text-sm md:w-full items-center after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:inline-block after:mx-4 xl:after:mx-8 ">
        <div class="flex items-center whitespace-nowrap">
            <span class={`w-8 h-8 ${protonCheckoutState.appState >= APP_STATE_PENDING_LOGIN ? 'bg-primary border-indigo-200': 'bg-gray-200 border-gray-200'}  border  rounded-full flex justify-center items-center text-sm text-white `}>1</span> 
            <p class={`${protonCheckoutState.appState >= APP_STATE_PENDING_LOGIN ? 'text-primary font-bold': ''} md:block hidden ml-3`}>Connect Webauth</p>
        </div>
    </li>
    <li class="flex text-sm md:w-full items-center after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:inline-block after:mx-4 xl:after:mx-8 ">
        <div class="flex items-center whitespace-nowrap ">
            <span class={`w-8 h-8 ${protonCheckoutState.appState >= APP_STATE_TOKEN_SELECT ? 'bg-primary border-indigo-200': 'bg-gray-200 border-gray-200'}  border  rounded-full flex justify-center items-center text-sm text-white `}>2</span> 
            <p class={`${protonCheckoutState.appState >= APP_STATE_TOKEN_SELECT ? 'text-primary font-bold': ''} md:block hidden ml-3`}>Select token</p>
        </div>
    </li>
    <li class="flex text-sm md:w-full items-center after:w-full after:h-1 after:border-b after:border-gray-200 after:border-1 after:inline-block after:mx-4 xl:after:mx-8 ">
        <div class="flex items-center whitespace-nowrap ">
          <span class={`w-8 h-8 ${protonCheckoutState.appState >= APP_STATE_TRANSFER_PROCESSING ? 'bg-primary border-indigo-200': 'bg-gray-200 border-gray-200'}  border  rounded-full flex justify-center items-center text-sm text-white `}>3</span> 
            <p class={`${protonCheckoutState.appState >= APP_STATE_TRANSFER_PROCESSING ? 'text-primary font-bold': ''} md:block hidden ml-3`}>Pay order</p>
        </div>
    </li>
    <li class="flex text-sm items-center text-gray-400 ">
        <div class="flex items-center  ">
          <span class={`w-8 h-8 ${protonCheckoutState.appState >= APP_STATE_TRANSFER_VERIFICATION ? 'bg-primary border-indigo-200': 'bg-gray-200 border-gray-200'}  border  rounded-full flex justify-center items-center text-sm text-white `}>4</span> 
            <p class={`${protonCheckoutState.appState >= APP_STATE_TRANSFER_VERIFICATION ? 'text-primary font-bold': ''} md:block hidden ml-3`}>Verify payment</p>
        </div>
    </li>
</ol>
</div>
<div class="bg-white p-2 md:p-8 ">
    {#if protonCheckoutState.appState == APP_STATE_PENDING_LOGIN}
      <div class="grid grid-cols-3 gap-8">
        <div class="grid grid-cols-1 grid-rows-[min-content,1fr,min-content] col-span-2 gap-4 card shadow-md p-8 rounded-none">
            <p class="text-md text-black">Already have WebAuth ?</p>
            <p class="text-2xl font-extrabold text-black">{params.translations.payInviteText}</p>
            <a style="text-decoration: none;" class="btn rounded-lg bg-black text-white flex justify-center items-center gap-4" on:click={()=>connectProton()}>
              <svg
                class=" fill-white"
                width="30"
                height="46.666666666666664"
                viewBox="0 0 61 37"
              >
                <path d="M17.6468 0C19.3753 0 20.5959 1.69349 20.0493 3.33337L14.8767 18.8512C14.4482 20.1366 15.1429 21.526 16.4284 21.9545C17.7138 22.383 19.1032 21.6883 19.5317 20.4028L25.7554 1.73167C26.1001 0.697533 27.0679 0 28.1579 0H34.1082C35.1983 0 36.166 0.697532 36.5108 1.73167L42.7345 20.4028C43.163 21.6883 44.5524 22.383 45.8378 21.9545C47.1232 21.526 47.8179 20.1366 47.3894 18.8512L42.2168 3.33337C41.6702 1.69349 42.8908 0 44.6194 0H54.1726C55.5372 0 56.7481 0.87435 57.1775 2.16956L60.3437 11.7209C60.8974 13.3914 60.7826 15.2114 60.0233 16.799L51.4488 34.7277C51.028 35.6075 50.1394 36.1675 49.1641 36.1675H40.3194C39.2293 36.1675 38.2615 35.47 37.9168 34.4358L32.8333 16.3361L32.8287 16.3222C32.8264 16.3152 32.824 16.3083 32.8217 16.3014C32.8174 16.2888 32.813 16.2763 32.8085 16.2638C32.6773 15.8987 32.4678 15.5832 32.2053 15.33C31.9842 15.116 31.7197 14.9409 31.4193 14.8198C31.1304 14.7029 30.8185 14.6404 30.4999 14.6405C29.8607 14.6404 29.2485 14.8921 28.7946 15.33C28.5321 15.5832 28.3226 15.8987 28.1914 16.2638C28.1828 16.2877 28.1745 16.3119 28.1666 16.3361L23.0831 34.4358C22.7383 35.47 21.7706 36.1675 20.6805 36.1675H11.8359C10.8606 36.1675 9.97203 35.6075 9.55125 34.7277L0.9767 16.799C0.217399 15.2114 0.102574 13.3914 0.656321 11.7209L3.82252 2.16956C4.25187 0.87435 5.46285 0 6.82737 0H17.6468Z"></path>
                <path d="M28.9933 25.0113C29.1997 24.3506 29.8117 23.9006 30.504 23.9006H30.7092C31.4015 23.9006 32.0135 24.3506 32.22 25.0113L32.8135 26.9107C33.132 27.93 32.3706 28.9657 31.3027 28.9657H29.9105C28.8426 28.9657 28.0812 27.93 28.3997 26.9107L28.9933 25.0113Z"></path>
              </svg>
              {params.translations.payInviteButtonLabel}
            </a>    
        </div>
        <div class="flex bg-black">
            <div class="grid-cols-[max-content,1fr] flex-col flex-grow justify-center w-full">
              <div class="flex flex-grow flex-col justify-between p-8">
                <div class="flex flex-col gap-4">
                <p class="text-md text-white">New to WebAuth ?</p>
                  <p class="text-2xl font-extrabold text-white">
                    The last wallet
                    <br />
                    you’ll need.
                  </p>
                  <!-- <svg
                    width="103"
                    height="15"
                    viewBox="0 0 103 15"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                  >
                    <path
                      fill-rule="evenodd"
                      clip-rule="evenodd"
                      d="M7.64061 1.44656C7.85221 0.811777 7.37973 0.15625 6.71061 0.15625H2.52256C1.99437 0.15625 1.52562 0.4947 1.35942 0.996062L0.133821 4.69328C-0.0805275 5.3399 -0.0360801 6.04441 0.257836 6.65896L3.57694 13.5989C3.73982 13.9395 4.08379 14.1562 4.46131 14.1562H7.88494C8.30689 14.1562 8.68151 13.8862 8.81494 13.4859L10.7827 6.47975C10.7858 6.47037 10.789 6.46104 10.7923 6.45176C10.8431 6.31045 10.9242 6.18833 11.0258 6.09031C11.2015 5.92079 11.4385 5.82335 11.6859 5.82339C11.8092 5.82337 11.93 5.84756 12.0418 5.89282C12.1581 5.93968 12.2605 6.00747 12.346 6.09031C12.4476 6.18833 12.5287 6.31045 12.5795 6.45176L12.5846 6.46631L12.5874 6.47436L12.5891 6.47975L14.5569 13.4859C14.6903 13.8862 15.065 14.1562 15.4869 14.1562H18.9106C19.2881 14.1562 19.6321 13.9395 19.795 13.5989L23.1141 6.65896C23.408 6.04441 23.4524 5.3399 23.2381 4.69328L22.0125 0.996062C21.8463 0.4947 21.3775 0.15625 20.8493 0.15625H17.1514C16.4823 0.15625 16.0098 0.811778 16.2214 1.44656L18.2236 7.45332C18.3895 7.95089 18.1206 8.48871 17.623 8.65457C17.1254 8.82042 16.5876 8.55151 16.4218 8.05394L14.0126 0.826556C13.8792 0.426256 13.5046 0.15625 13.0826 0.15625H10.7794C10.3574 0.15625 9.9828 0.426257 9.84936 0.826557L7.44024 8.05394C7.27438 8.55151 6.73656 8.82042 6.23899 8.65457C5.74141 8.48871 5.4725 7.95089 5.63836 7.45332L7.64061 1.44656ZM11.6877 9.4078C11.4197 9.4078 11.1828 9.58196 11.1029 9.83774L10.8731 10.573C10.7498 10.9675 11.0446 11.3684 11.4579 11.3684H11.9968C12.4102 11.3684 12.7049 10.9675 12.5816 10.573L12.3519 9.83774C12.2719 9.58196 12.0351 9.4078 11.7671 9.4078H11.6877Z"
                      fill="white"
                    />
                    <path
                      d="M33.6507 13.1771L29.958 0.271484H32.9387L35.0749 9.23864H35.182L37.5388 0.271484H40.091L42.4414 9.25754H42.5549L44.6911 0.271484H47.6718L43.979 13.1771H41.3198L38.8621 4.73931H38.7613L36.31 13.1771H33.6507Z"
                      fill="white"
                    />
                    <path
                      d="M51.5658 13.3662C50.5702 13.3662 49.7132 13.1645 48.9948 12.7612C48.2806 12.3537 47.7303 11.7782 47.3438 11.0346C46.9573 10.2868 46.764 9.40248 46.764 8.38162C46.764 7.38597 46.9573 6.51215 47.3438 5.76016C47.7303 5.00817 48.2743 4.42213 48.9759 4.00202C49.6816 3.58192 50.5093 3.37186 51.4587 3.37186C52.0973 3.37186 52.6917 3.47479 53.242 3.68064C53.7966 3.88229 54.2797 4.18687 54.6914 4.59437C55.1073 5.00187 55.4308 5.5144 55.6618 6.13196C55.8929 6.74531 56.0084 7.46369 56.0084 8.2871V9.02438H47.8353V7.36077H53.4815C53.4815 6.97427 53.3975 6.63188 53.2294 6.33361C53.0614 6.03533 52.8282 5.80217 52.53 5.63413C52.2359 5.46189 51.8935 5.37577 51.5028 5.37577C51.0953 5.37577 50.734 5.47029 50.4189 5.65934C50.1081 5.84418 49.8644 6.09415 49.6879 6.40923C49.5115 6.7201 49.4212 7.06669 49.417 7.44899V9.03069C49.417 9.50961 49.5052 9.92341 49.6816 10.2721C49.8623 10.6208 50.1165 10.8897 50.4441 11.0787C50.7718 11.2677 51.1604 11.3623 51.6099 11.3623C51.9082 11.3623 52.1813 11.3203 52.4291 11.2362C52.677 11.1522 52.8892 11.0262 53.0656 10.8581C53.242 10.6901 53.3765 10.4843 53.4689 10.2406L55.9517 10.4044C55.8257 11.001 55.5673 11.5219 55.1766 11.9672C54.7901 12.4083 54.2902 12.7528 53.6769 13.0007C53.0677 13.2443 52.364 13.3662 51.5658 13.3662Z"
                      fill="white"
                    />
                    <path
                      d="M56.9234 13.1771V0.271484H59.6079V5.1237H59.6898C59.8075 4.86324 59.9776 4.59857 60.2003 4.3297C60.4271 4.05664 60.7212 3.82978 61.0825 3.64913C61.448 3.46429 61.9017 3.37186 62.4436 3.37186C63.1494 3.37186 63.8006 3.55671 64.3971 3.9264C64.9937 4.29189 65.4705 4.84433 65.8276 5.58372C66.1847 6.3189 66.3632 7.24104 66.3632 8.35011C66.3632 9.42979 66.1889 10.3414 65.8402 11.085C65.4957 11.8244 65.0252 12.3852 64.4286 12.7675C63.8363 13.1456 63.1725 13.3347 62.4373 13.3347C61.9164 13.3347 61.4732 13.2485 61.1077 13.0763C60.7464 12.9041 60.4502 12.6877 60.2192 12.4272C59.9881 12.1626 59.8117 11.8958 59.6898 11.6269H59.5701V13.1771H56.9234ZM59.5512 8.33751C59.5512 8.91306 59.631 9.41508 59.7906 9.84359C59.9503 10.2721 60.1813 10.6061 60.4838 10.8455C60.7863 11.0808 61.1539 11.1984 61.5866 11.1984C62.0235 11.1984 62.3932 11.0787 62.6957 10.8392C62.9982 10.5956 63.2271 10.2595 63.3825 9.83099C63.5422 9.39828 63.622 8.90045 63.622 8.33751C63.622 7.77877 63.5443 7.28725 63.3889 6.86294C63.2334 6.43863 63.0045 6.10675 62.702 5.86729C62.3995 5.62783 62.0277 5.5081 61.5866 5.5081C61.1497 5.5081 60.78 5.62363 60.4775 5.85469C60.1792 6.08575 59.9503 6.41343 59.7906 6.83773C59.631 7.26204 59.5512 7.76197 59.5512 8.33751Z"
                      fill="white"
                    />
                    <path
                      d="M68.7039 13.1771H66.637L71.2813 0.271484H73.5309L78.1752 13.1771H76.1083L72.4597 2.61567H72.3588L68.7039 13.1771ZM69.0505 8.12326H75.7554V9.76167H69.0505V8.12326Z"
                      fill="white"
                    />
                    <path
                      d="M85.1318 9.16302V3.4979H87.0223V13.1771H85.1696V11.5009H85.0688C84.8462 12.0176 84.4891 12.4482 83.9976 12.7927C83.5102 13.133 82.9032 13.3032 82.1764 13.3032C81.5546 13.3032 81.0043 13.1666 80.5254 12.8936C80.0507 12.6163 79.6768 12.2067 79.4037 11.6647C79.1348 11.1228 79.0004 10.4527 79.0004 9.65454V3.4979H80.8846V9.42768C80.8846 10.0873 81.0673 10.6124 81.4328 11.0031C81.7983 11.3938 82.273 11.5891 82.857 11.5891C83.2099 11.5891 83.5606 11.5009 83.9093 11.3245C84.2622 11.148 84.5542 10.8813 84.7852 10.5242C85.0205 10.1671 85.136 9.71336 85.1318 9.16302Z"
                      fill="white"
                    />
                    <path
                      d="M93.1849 3.4979V5.01028H87.8979V3.4979H93.1849ZM89.3158 1.17891H91.1999V10.3351C91.1999 10.7006 91.2546 10.9758 91.3638 11.1606C91.473 11.3413 91.6137 11.4652 91.786 11.5324C91.9624 11.5954 92.1536 11.6269 92.3594 11.6269C92.5107 11.6269 92.643 11.6164 92.7564 11.5954C92.8699 11.5744 92.9581 11.5576 93.0211 11.545L93.3614 13.1015C93.2522 13.1435 93.0967 13.1855 92.8951 13.2275C92.6934 13.2738 92.4414 13.299 92.1389 13.3032C91.6432 13.3116 91.181 13.2233 90.7525 13.0385C90.324 12.8536 89.9774 12.568 89.7128 12.1815C89.4481 11.795 89.3158 11.3098 89.3158 10.7258V1.17891Z"
                      fill="white"
                    />
                    <path
                      d="M96.4741 7.43008V13.1771H94.5899V0.271484H96.4488V5.07329H96.5686C96.7954 4.55236 97.142 4.13856 97.6083 3.83188C98.0747 3.5252 98.6838 3.37186 99.4358 3.37186C100.1 3.37186 100.679 3.5084 101.175 3.78147C101.675 4.05454 102.061 4.46204 102.335 5.00397C102.612 5.54171 102.75 6.21388 102.75 7.02048V13.1771H100.866V7.24734C100.866 6.53736 100.684 5.98702 100.318 5.59632C99.9525 5.20142 99.4442 5.00397 98.793 5.00397C98.3477 5.00397 97.9486 5.0985 97.5957 5.28755C97.247 5.47659 96.9719 5.75386 96.7702 6.11935C96.5728 6.48065 96.4741 6.91755 96.4741 7.43008Z"
                      fill="white"
                    />
                  </svg> -->
                  
                  <button class="btn rounded-lg bg-white w-full font-normal">
                    Get it Now
                  </button>
                </div>
                
              </div>
              
            </div>
        </div>
      </div>
      
      
    {/if}
    
    
    {#if protonCheckoutState.appState == APP_STATE_TOKEN_SELECT}
    <PayTokenSelector 
        isTestnet={params.testnet.toString()=="1"}
        storeCurrency={params.wooCurrency} 
        cartAmount={params.order.total.toString()} 
        changeSession={changeAccount} 
        selectPayToken={(token,amount)=>initTransfer(token,amount)} 
        allowedTokens={params.allowedTokens} 
        actorName={protonCheckoutState.session.auth.actor.toString()}
        baseDomain={params.baseDomain}
        translations={{
          processingLabel:params.translations.selectTokenPayProcessingLabel,
          payLabel:params.translations.selectTokenPayButtonLabel,
          connectedAdLabel:params.translations.selectTokenDialogConnectedAs,
          changeAccountLabel:params.translations.selectTokenDialogChangeAccountLabel
        }}
        />
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_TRANSFER_PROCESSING}
      <Processing label={'Waiting for transfer to complete'}></Processing>
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION}
        <PaymentVerify 
        network={params.testnet?'testnet':'mainnet'}
        paymentKey={params.order.paymentKey} 
        actor={protonCheckoutState.session.auth.actor.toString()} 
        onVerify={onPaymentVerify}
        baseDomain={params.baseDomain}
        translations={{
          processingLabel:params.translations.verifyPaymentDialogProcessLabel,
          verifyText:params.translations.verifyPaymentDialogText
        }}
        ></PaymentVerify>
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_TRANSFER_VERIFICATION_SUCCESS}
    <PaymentSucceed redirectUrl={params.order.continueRedirect} translations={{title:params.translations.verifySuccessPaymentDialogTitle,text:params.translations.verifySuccessPaymentDialogText}}></PaymentSucceed>
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_TRANSFER_FAILURE}
    <PaymentFailure onRetry={()=>setAppState(APP_STATE_TOKEN_SELECT)} translations={{title:params.translations.paymentFailureDialogTitle,text:params.translations.paymentFailureDialogText}}></PaymentFailure>
    {/if}
    {#if protonCheckoutState.appState == APP_STATE_INVALID_ORDER}
    <InvalidOrder translations={{title:params.translations.invalidOrderDialogTitle,text:params.translations.invalidOrderDialogText}}></InvalidOrder>
    {/if}
    
  
</div>

</main>

<style>
   :global( [slot="content"] ) {

    max-height: 100%;
    display: grid;
    grid-template-rows: min-content 1fr min-content;
    gap:10px;

  }

  .brand__icon {
    width: 60px;
    height: 60px;
    background-size: cover;
    background-position: center center;
    border-radius: 50%;
    overflow: hidden;
    border: 3px solid #7c3bed;
    background-image: url("./../../images/brand_logo.png");
    
  }

</style>
