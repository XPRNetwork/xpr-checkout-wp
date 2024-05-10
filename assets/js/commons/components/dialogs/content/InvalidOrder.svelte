<script lang="ts">
  export let translations:any = {
    text:""
  }
  
</script>
<div >
  <div class="grid grid-cols-[min-content,1fr] gap-4">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-16 h-16 fill-red-400">
      <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />
    </svg>
    
    <div class="flex flex-col gap-2">
      <p class="text-2xl font-extrabold">{translations.title}</p>
      <p>{translations.text}</p>
    </div>
    
    
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

.payment-failure__wrapper {

    padding: 0;
    margin: 0;
    border-radius: 0;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    gap:10px;

}



</style>