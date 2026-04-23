<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div style="z-index:1050;" v-if="alert.show" 
       :class="`alert alert-${alert.type} alert-dismissible fade show position-fixed top-0 end-0 m-3 z-5`"
       role="alert">
    {{ alert.message }}
    <button type="button" class="btn-close" @click="alert.show = false"></button>
  </div>
</template>

<script setup>
import { reactive,defineExpose } from 'vue'
const alert = reactive({
  show: false,
  message: '',
  type: 'success'
})

/**
 * Open Alert 3 second
 * 
 * @param string message
 * @param string type
 */
const open = (message, type = 'success') => {
  alert.message = message
  alert.type = type
  alert.show = true
  setTimeout(() => alert.show = false, 3000)
}

defineExpose({ open })
</script>
