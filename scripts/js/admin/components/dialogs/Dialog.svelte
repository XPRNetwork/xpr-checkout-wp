<script lang="ts">

  export let title = "";
  export let onClose = ()=>{};
  export let open:boolean = false

  function closeCheckoutModal(){

    open = false

  }

</script>



{#if open}
<div class="dialog">
  <div class="dialog__backdrop"></div>
  <div class="dialog__content">
    <div class="dialog__content__head">
      <slot name="head" />
      <button on:click={closeCheckoutModal} class="dialog__close">
        <svg width="30" height="30" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.7816 4.03157C12.0062 3.80702 12.0062 3.44295 11.7816 3.2184C11.5571 2.99385 11.193 2.99385 10.9685 3.2184L7.50005 6.68682L4.03164 3.2184C3.80708 2.99385 3.44301 2.99385 3.21846 3.2184C2.99391 3.44295 2.99391 3.80702 3.21846 4.03157L6.68688 7.49999L3.21846 10.9684C2.99391 11.193 2.99391 11.557 3.21846 11.7816C3.44301 12.0061 3.80708 12.0061 4.03164 11.7816L7.50005 8.31316L10.9685 11.7816C11.193 12.0061 11.5571 12.0061 11.7816 11.7816C12.0062 11.557 12.0062 11.193 11.7816 10.9684L8.31322 7.49999L11.7816 4.03157Z" fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"></path></svg></button>
    </div>
    <div class="dialog__content__body">
      <slot name="content" />
    </div>
  </div>
</div>
{/if}

<style lang="scss">
  .dialog {

  display: block;
  position:fixed;
  top: 0;
  left: 0;
  right:0;
  bottom:0;
  z-index: 2000;

  }
  .dialog__backdrop {
    position:absolute;
    top: 0;
    left: 0;
    right:0;
    bottom:0;
    background-color: rgba(0,0,0,0.5);
    z-index: 1;

  }

  .dialog__content {
    position:absolute;
    left:50%;
    top: 50%;
    max-height: 80vh;
    width: 30%;
    transform: translate(-50%,-50%);
    background-color: white;
    z-index: 1;
    display: grid;
    grid-template-columns: 1fr;
    gap: 10px;
    padding: 10px;
  }

  .dialog__close {

      width: 30px;
      height: 30px;
      padding: 0;
      margin: 0;
      border-radius: 0;
      background-color: transparent;
      border: none;

}
  .dialog__content__head {
    display: grid;
    grid-template-columns: 1fr min-content;
    gap: 10px;
    padding: 10px;
    align-items: center;

  }
  .dialog__content__body {

      display: grid;
      grid-template-columns: 1fr;
      gap: 10px;
      padding: 10px;

  }
</style>