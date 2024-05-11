<script lang="ts">
  export let status = 0;
  export let variant:string = ''
</script>
{#if status  == -2}
  <span class={`status-chip refunded ${variant}`}>
    Refunded
  </span>
  {/if}
  {#if status  == -1}
  <span class={`status-chip canceled ${variant}`}>
    Canceled
  </span>
  {/if}
  {#if status  == 0}
  <span class={`status-chip ${variant}`}>
    Await
  </span>
  {/if}
  {#if status  == 1}
  <span class={`status-chip fulfilled ${variant}`}>
    Fulfilled
  </span>
  {/if}
  {#if status  == 2}
  <span class={`status-chip payed-out ${variant}`}>
    Paid out
  </span>
  {/if}