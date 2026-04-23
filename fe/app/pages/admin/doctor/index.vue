<template>
  <div v-if="!isLoggedIn" class="d-flex align-items-center justify-content-center min-vh-100">
    <Nologin />
  </div>
  <div v-else>
    <Table @confirm="handleConfirm" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { useRouter } from "vue-router"
import Table from "~/components/user/doctor/Table.vue"
import Nologin from "~/components/NoLogin.vue"

const isLoggedIn = ref(false)
const router = useRouter()

onMounted(() => {
  const storedUser = localStorage.getItem("user")
  isLoggedIn.value = !!storedUser
})

function handleConfirm(item) {
  if (!item.id) return
  router.push(`/admin/doctor/profile-detail/${item.id}`)
}
</script>
