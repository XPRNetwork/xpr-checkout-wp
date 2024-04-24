<script lang="ts">
 import  {onMount} from 'svelte';
  import { verifyPayment } from '../../../services/VerifyPayment';
  import Processing from '../../processing/Processing.svelte';
  
  import type { WPResponse,Order } from '../../../type';

 export let paymentKey = '';
 export let actor = '';
 export let network = "testnet"
 export let baseDomain = "";
 export let onVerify:(result:WPResponse<Order>)=>void = (result:WPResponse<Order>)=>{}
 export let translations:any = {
  processingLabel:"",
  verifyText:""

 }


 onMount(async ()=>{

  const paymentVerifyResult = await verifyPayment(baseDomain,paymentKey,actor,network);
  onVerify(paymentVerifyResult.data);
  
 })

</script>

<div class="payment-verify dialog-content">
  <Processing label={translations.processingLabel}></Processing>
  <p >{translations.verifyText}</p>
</div>
