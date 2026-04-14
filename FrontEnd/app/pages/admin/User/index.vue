<script setup>
import { onMounted, inject } from "vue"
import { useRouter } from "vue-router"
import ListSearchUser from "@/components/user/ListSearchUser.vue"

const router = useRouter()
const alert = inject("alert")

// Auth/role guard only
onMounted(() => {
  const user = JSON.parse(localStorage.getItem("user") || "{}")
  if (user.id_role !== 1) {
    if (alert) alert("Bạn không có quyền truy cập!")
    router.push("/admin")
  }
})
</script>

<template>
  <div class="p-6">
    <ListSearchUser />
  </div>
</template>
