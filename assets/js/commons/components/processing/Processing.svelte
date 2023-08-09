<script lang="ts">
  import './../../../../styles/components/processing.scss'
  export let label:string = ''
</script>

<div class="processing">
  <div class="processing__icon">
    
  </div>
  <h4 class="processing__label">{label}</h4>
</div>
<style>
  
  .processing__icon {
    background-image: url("./../../../../images/processing.gif");
    
  }
</style>