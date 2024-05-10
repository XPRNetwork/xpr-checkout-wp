<script lang="ts">
  import './../../../../styles/components/processing.scss'
  export let label:string = ''
</script>

<div class="processing">
  <div class="processing__icon">
    
  </div>
  <p class="text-sm font-bold text-primary">{label}</p>
</div>
<style>
  
  .processing__icon {
    background-image: url("./../../../../images/processing.gif");
    
  }
</style>