<script lang="ts">
 import  {onMount} from 'svelte';
  import { verifyPayment } from '../../../services/VerifyPayment';
  import Processing from '../../processing/Processing.svelte';

 export let paymentKey = '';
 export let transactionId = '';
 export let network = "testnet"
 export let baseDomain = "";
 export let onVerify:(result:any)=>void = (result:any)=>{}
 export let translations:any = {
  processingLabel:"",
  verifyText:""

 }


 onMount(async ()=>{

  const paymentVerifyResult = await verifyPayment(baseDomain,paymentKey,transactionId,network);
  onVerify(paymentVerifyResult.data);
  
 })

</script>

<div class="payment-verify dialog-content">
  <Processing label={translations.processingLabel}></Processing>
  <p >{translations.verifyText}</p>
</div>
