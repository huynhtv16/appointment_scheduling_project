<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-success  bg-success-patient shadow-sm">
    <div class="container-fluid">
      <!-- Logo -->
      <a class="navbar-brand d-flex align-items-center ps-5" href="#">
        <img
          src="../../assets/images/logo.png"
          alt="Logo"
          width="60"
         
          class="d-inline-block align-text-top me-2"
        />
      </a>

      <!-- Toggle button mobile -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Menu -->
      <div class="collapse navbar-collapse" id="navbarNav">
        <!-- Menu links -->
        <ul class="navbar-nav ms-auto align-items-center text-center">
          <li class="nav-item li-header-patient">
            <nuxt-link to="/clients" class="nav-link fw-medium d-flex align-items-center">Trang chủ</nuxt-link>
          </li>
          <li class="nav-item li-header-patient">
            <nuxt-link to="/clients/appointment" class="nav-link fw-medium d-flex align-items-center">Đăng kí khám</nuxt-link>
          </li>
          <li class="nav-item li-header-patient">
            <nuxt-link to="/clients/history" class="nav-link fw-medium d-flex align-items-center">Lịch sử</nuxt-link>
          </li>

          <!-- Dropdown user -->
          <li class="nav-item dropdown li-header-patient">
            <a
              class="nav-link dropdown-toggle d-flex align-items-center"
              href="#"
              id="dropdownUser"
              role="button"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-person fs-5 me-2"></i>
              <div class="text-start lh-1">
                <div class="fw-semibold small">{{ user ? user.name : "Đăng nhập" }}</div>
                <div class="text-muted small">{{ user ? user.role : "" }}</div>
              </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="dropdownUser">
              <li v-if="user" class="li-header-patient-coponent">
                <nuxt-link :to="`/clients/${user.id}`" class="dropdown-item">Cập nhật tài khoản</nuxt-link>
              </li>
              <li v-if="user" class="li-header-patient-coponent">
                <a href="#" class="dropdown-item" @click="logout">Đăng xuất</a>
              </li>
              <li v-else class="li-header-patient-coponent">
                <nuxt-link to="/clients/login" class="dropdown-item">Đăng nhập</nuxt-link>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted } from "vue";

const user = ref(null);

//Onmouted get info patient login
onMounted(() => {
  const storedUser = localStorage.getItem("patient");
  if (storedUser) {
    user.value = JSON.parse(storedUser);
  }
});
//logout
const logout = () => {
  localStorage.removeItem("patient");
  user.value = null;
  window.location.href = "/clients/login"; 
};
</script>

<style>

.h-52 {
  min-height: 64px;
}
.li-header-patient{
  padding-left: 40px ;
  font-size: 18px;
}
.gap-26 {
  gap: 26px !important;
}
.navbar {
  z-index: 1; 
}
.navbar a
{
  text-decoration: none !important;
  color: rgb(255, 255, 255) !important;
}
.navbar a:hover{
  color:#fffb00!important
}
.navbar .router-link-exact-active {
  color: #fffb00!important;
  font-weight: bold;
}
.li-header-patient-coponent a{
  color: #3b383b !important;
}
.li-header-patient-coponent:hover a{
  color: #3b383b !important;
}
</style>
