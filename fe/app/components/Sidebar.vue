<!-- eslint-disable vue/multi-word-component-names -->
<script setup>
// Imports
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"

// Router instance for navigation
const router = useRouter()

// Reactive object to store current user info
const user = ref({})

/**
 * On component mount, load user data from localStorage
 * If exists, parse JSON and set to `user`
 */
onMounted(() => {
  const data = localStorage.getItem("user")
  if (data) {
    user.value = JSON.parse(data)
  }
})

/**
 * Handle logout
 * Removes user and token from localStorage
 * Redirects to admin login page
 */
const handleLogout = () => {
  localStorage.removeItem("user")
  localStorage.removeItem("token")
  router.push("/admin/login")
}
</script>

<template>
  <div class="h-100">
    <!-- Button toggle -->
    <button class="btn btn-primary d-lg-none m-2" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu"
      aria-expanded="false" aria-controls="sidebarMenu">
      <i class="bi bi-list"></i>
    </button>

    <!-- Sidebar -->
    <div class="collapse d-lg-block sidebar p-3 h-100" id="sidebarMenu">
      <ul class="nav flex-column">
        <li class="nav-item mt-2">
          <NuxtLink to="/admin" class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-house me-2 fs-6"></i> Trang chủ
          </NuxtLink>
        </li>
      </ul>

      <!-- Main menu -->
      <h6 class="text-secondary text-uppercase fs-6 fw-semibold mt-4 mb-2">
        Main Menu
      </h6>
      <ul class="nav flex-column">
        <li v-if="user.id_role === 3 || user.id_role === 1" class="nav-item mt-2">
          <NuxtLink to="/admin/staff" class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-alarm me-2"></i> Danh sách ca hẹn
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 2" class="nav-item mt-2">
          <NuxtLink to="/admin/doctor" class="nav-link fs-6">
            <i class="bi-heart-pulse me-2"></i> Khám bệnh
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 2|| user.id_role === 1" class="nav-item mt-2">
          <NuxtLink to="/admin/doctor/history" class="nav-link fs-6" active-class="active-link">
            <i class="bi-heart-pulse me-2"></i> Lịch sử khám bệnh
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 1" class="nav-item mt-2">
          <NuxtLink to="/admin/patient" class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-people me-2"></i> Quản lý bệnh nhân
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 1" class="nav-item mt-2">
          <NuxtLink to="/admin/user" class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-person-lines-fill me-2"></i> Quản lý người dùng
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 1" class="nav-item mt-2">
          <NuxtLink to="/admin/statis" class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-graph-up me-2"></i> Quản lý thống kê
          </NuxtLink>
        </li>
        <li v-if="user.id_role === 1" class="nav-item mt-2">
          <NuxtLink class="nav-link fs-6" active-class="active-link">
            <i class="bi bi-bell me-2"></i> Quản lý thông báo
          </NuxtLink>
        </li>
      </ul>

      <!-- Support -->
      <h6 class="text-secondary text-uppercase fs-6 fw-semibold mt-4 mb-2">
        Support
      </h6>
      <ul class="nav flex-column">
        <li class="nav-item mt-2">
          <NuxtLink class="nav-link fs-6">
            <i class="bi bi-gear me-2"></i> Setting
          </NuxtLink>
        </li>
        <li class="nav-item mt-2">
          <NuxtLink class="nav-link fs-6">
            <i class="bi bi-question-circle me-2"></i> Help
          </NuxtLink>
        </li>
        <li class="nav-item mt-2">
          <a href="#" class="nav-link fs-6" @click.prevent="handleLogout">
            <i class="bi bi-box-arrow-right me-2"></i> Sign Out
          </a>
        </li>
      </ul>
    </div>
  </div>
</template>
<style src="@/assets/css/Sidebar.css"></style>
