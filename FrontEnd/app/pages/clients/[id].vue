<template>
  <div>
    <CreateEditPatient mode="update" :id="id" />
  </div>
</template>

<script setup>
definePageMeta({ layout: "patient" })
import CreateEditPatient from "~/components/patient/CreateEditPatient.vue"
import { useRoute } from "vue-router"

const route = useRoute()
const id = route.params.id
</script>
