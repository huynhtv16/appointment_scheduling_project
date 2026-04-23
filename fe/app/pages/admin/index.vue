<template>
  <Home @error="handleError" />
  
</template>

<script setup>
import { onMounted, inject } from "vue"
import Home from "@/components/Home/Home.vue"
import { useRouter } from "vue-router"

const router = useRouter()
const alert = inject("alert")

/**
 * Check auth on mount and redirect to login if needed
 */
onMounted(() => {
  const token = localStorage.getItem("token")
  if (!token) {
    alert("Vui lòng đăng nhập")  
    router.push("/admin/login")       
    return
  }
})

const handleError = (message) => {
  alert(message, "danger")
}
</script>

