<script lang="ts">
  export let translations:any = {
    text:""
  }

  export let redirectUrl:string
  
</script>
<div class="payment-failure">
  <div class="grid grid-cols-[min-content,1fr] gap-4">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 fill-green-400">
      <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
    </svg>
    
    <div class="flex flex-col gap-2">
      <p class="text-2xl font-extrabold">{translations.title}</p>
      <p>{translations.text}</p>
    </div>
    
    
  </div>
  <div class="flex justify-end gap-4">
    <a class="btn btn-primary rounded-md" href={redirectUrl}>Continue</a>
  </div>
  
  
</div>

<style>
  .payment-failure {

    width: 100%;
    display: grid;
    justify-content: flex-start;
    align-items: flex-start;
    grid-template-columns: 1fr;
    gap:10px;

}





</style>