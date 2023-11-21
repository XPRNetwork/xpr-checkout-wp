<script lang="ts">

  import {createEventDispatcher} from 'svelte';
  const dispatch = createEventDispatcher();

  if (window && window.jQuery) { 

  
  window.jQuery('input').on('blur', function () {

    dispatch('formEvent',validateCheckoutForm())

   })

  function validateCheckoutForm():boolean {
  
    let invalidCount = 0
    if (window && window.jQuery) {
      window.jQuery('.validate-required input, .validate-required select').trigger('validate');
      jQuery('.woocommerce-invalid').each(
        function (index, element) {
          if (element) invalidCount++
        }
      )
    } else {
      return true
    }
    return invalidCount == 0

  }

}
</script>