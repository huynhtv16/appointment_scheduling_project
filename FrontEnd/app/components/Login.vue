<!-- eslint-disable vue/multi-word-component-names -->
<template>
  <div class="col-md-5">
    <div class="card shadow-lg rounded-4 p-4 border-0">
      <h3 class="fw-bold text-center mb-4" style="color:#1b3c74;">Đăng nhập</h3>

      <form @submit.prevent="handleSubmit">
        <div class="mb-3">
          <label class="form-label">Gmail</label>
          <input v-model="form.gmail" type="text" class="form-control rounded-pill" placeholder="Nhập Gmail">
        </div>

        <div class="mb-3">
          <label class="form-label">Mật khẩu</label>
          <div class="input-group">
            <input v-model="form.password" type="password" class="form-control rounded-pill"
              placeholder="Enter your Password">
            <span class="input-group-text bg-white border-0"><i class="bi bi-eye"></i></span>
          </div>
        </div>

        <div class="d-flex justify-content-between mb-3">
          <a v-if="data.mode === 'patient'" href="/clients/new" class="small text-decoration-none">Đăng kí tài khoản</a>
          <a href="#" class="small text-decoration-none">Quên mật khẩu</a>
        </div>

        <button type="submit" class="btn w-100 rounded-pill py-2 fw-bold" style="background:#00AEEF; color:white;">
          Đăng nhập
        </button>
      </form>
    </div>
  </div>
</template>

<script setup>
// Imports
import { reactive, defineProps, inject } from 'vue';
import { useRouter } from "vue-router";
import { APISERVER } from '../const/api';

// Router instance for navigation
const router = useRouter()

// Alert function injected from parent or global provider
const alert = inject('alert');

// Props to define the login mode (patient or admin)
const data = defineProps(["mode"]);

// Reactive form data for login
const form = reactive({
  gmail: "",
  password: "",
})

/**
 * Handle login submission
 * Behavior depends on `mode` prop: "patient" or "admin"
 * Saves user info and token to localStorage
 * Navigates to appropriate dashboard
 */
const handleSubmit = async () => {
  if (data.mode === "patient") {
    try {
      const response = await $fetch(`${APISERVER}/patient/login`, {
        method: "POST",
        body: form
      })

      const token = response.data.access_token
      const user = response.data.patient

      // Store token and user in localStorage
      localStorage.setItem("patient_token", token)
      localStorage.setItem("patient", JSON.stringify(user))

      console.log("Đăng nhập thành công:", user)
      alert(response.message, "success")
      router.push("/clients")
    } catch (err) {
      const message = err?.errors || err?.data?.message || err?.message || err
      console.error("Lỗi đăng nhập:", message)
      alert(message, "danger")
    }
  } else if (data.mode === "admin") {
    try {
      const response = await $fetch(`${APISERVER}/user/login`, {
        method: "POST",
        body: form
      })

      const token = response.data.access_token
      const user = response.data.user

      // Check account status
      if (user?.status !== 1) {
        alert("Tài khoản bị khóa, vui lòng liên hệ quản trị viên!", "danger")
        return
      }

      // Store token and user info
      localStorage.setItem("token", token)
      localStorage.setItem("user", JSON.stringify(user))

      console.log("Login success:", user)
      alert(response.message, "success")

      // Navigate based on role
      switch (user.id_role) {
        case 1:
          router.push("/admin")
          break
        case 2:
          router.push("/admin/doctor")
          break
        case 3:
          router.push("/admin/staff")
          break
        default:
          alert("Bạn không có quyền truy cập hệ thống!", "danger")
          localStorage.clear()
          break
      }

    } catch (err) {
      const message = err?.errors || err?.data?.message || err?.message || err
      console.error("Lỗi đăng nhập:", message)
      alert(message, "danger")
    }
  }
}
</script>
