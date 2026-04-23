<template>
  <header class="bg-cl h-52 d-flex align-items-center px-3">
    <div class="container-fluid">
      <div class="row w-100 align-items-center">
        <div class="col-6 col-lg-3 col-xl-2 d-flex align-items-center justify-content-start justify-content-lg-center">
          <img src="../../assets/images/logo.png" 
              alt="Logo" 
              class="img-fluid" 
              style="width: 65px; object-fit: cover;" />
        </div>
        <div class="col-lg-5 col-xl-6 d-none d-lg-block text-center">
        </div>
        <div class="col-6 col-lg-4 col-xl-4 d-flex align-items-center justify-content-end gap-3 gap-lg-4">
          <div class="text-white d-flex align-items-center gap-3 gap-lg-4 fs-6 fs-lg-5">
            <i class="bi bi-bell"></i>
            <i class="bi bi-envelope"></i>
          </div>

          <span class="text-white d-none d-lg-inline">|</span>

          <div class="text-white d-none d-lg-flex align-items-center gap-3 gap-lg-4 fs-6 fs-md-5">
            <i class="bi bi-translate"></i>
            <i class="bi bi-brightness-high"></i>
          </div>

          <span class="text-white d-none d-lg-inline">|</span>

          <div class="d-flex align-items-center gap-2 gap-lg-3">
            <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px;">
              <img src="https://picsum.photos/400" class="w-100 h-100 " alt="avatar" />
            </div>
            <div class="text-white lh-1 d-none d-lg-block">
              <p class="mb-0 fw-bold">{{ username }}</p>
              <small>{{ getRoleName(role) }}</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>



<script setup>
import { ref, onMounted } from 'vue'
import { getRoleName } from '../../const/roles'

const user = ref(null)
const username = ref('')
const role = ref('')

/**
 * onMounted lifecycle hook
 * Runs after the component is mounted
 * Loads the user data from localStorage
 * Parses it and assigns values to reactive references
 */
onMounted(() => {
  const userStr = localStorage.getItem('user')  
  if (userStr) {
    try {
      user.value = JSON.parse(userStr) 
      username.value = user.value.name 
      role.value = user.value.id_role 
    } catch (error) {
      console.error('Lỗi khi phân tích cú pháp người dùng từ localStorage:', error)
    }
  }
})
</script>


<style src="@/assets/css/headerAdmin.css"></style>

