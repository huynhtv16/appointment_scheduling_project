<template>
  <div>
    <CreateAppointments v-if="isReady && isLoggedIn" />
    <Nologin v-else-if="isReady" />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import CreateAppointments from "~/components/patient/CreateAppointments.vue"
import Nologin from "~/components/NoLogin.vue"

definePageMeta({ layout: "patient" })

const isLoggedIn = ref(false)
const isReady = ref(false)

//Onmouted get infor patient login
onMounted(() => {
  if (process.client) {
    const storedUser = localStorage.getItem("patient")
    isLoggedIn.value = !!storedUser
    isReady.value = true
  }
})
</script>
  