<template>
<PatientListSearch ref="patientList" />
</template>

<script setup>
import { onMounted, inject } from "vue"
import PatientListSearch from "~/components/patient/PatientListSearch.vue"

/**
 * Page metadata
 */
definePageMeta({ layout: "default" })

const alert = inject("alert")

onMounted(() => {
  const token = localStorage.getItem("token")
  if (!token) {
    if (alert) alert("Bạn chưa đăng nhập", "danger")
  }
})
</script>
