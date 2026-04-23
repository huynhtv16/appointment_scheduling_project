<template>
  <div v-if="visible" class="confirm-dialog position-fixed top-50 start-50 translate-middle bg-black bg-opacity-50 d-flex align-items-center justify-content-center">
    <div class="bg-white p-4 rounded shadow" style="width: 400px;">
      <h2 class="h5 mb-3">{{ title }}</h2>
      <p class="mb-4">{{ message }}</p>
      <div class="d-flex justify-content-end gap-2">
        <button class="btn btn-secondary" @click="cancel">Hủy</button>
        <button class="btn btn-primary" @click="confirm">Xác nhận</button>
      </div>
    </div>
  </div>
</template>

<script setup>

import { ref, defineExpose } from 'vue'

const visible = ref(false)
const title = ref('')
const message = ref('')
let resolveFn, rejectFn


/**
 * Open dialog
 * 
 * @param string title
 * @param string message
 */
const open = (t, m) => {
  title.value = t
  message.value = m
  visible.value = true
  return new Promise((resolve, reject) => {
    resolveFn = resolve
    rejectFn = reject
  })
}

/**
 * confirm dialog
 */
const confirm = () => {
  visible.value = false
  resolveFn(true)
}

/**
 * cancel/ close dialog
 */
const cancel = () => {
  visible.value = false
  rejectFn(false)
}

defineExpose({ open })
</script>

<style>
.confirm-dialog{
  width:100vw;
  height:100vh; 
  z-index:101;
}
</style>