<template>
  <div >
    <History v-if="isLoggedIn" />
    <Nologin v-else />
  </div>
</template>

<script setup>
definePageMeta({
  layout: "patient",
});

import { ref, onMounted } from "vue";
import History from "~/components/patient/History.vue";
import Nologin from "~/components/NoLogin.vue";

const isLoggedIn = ref(false);

onMounted(() => {
  const storedUser = localStorage.getItem("patient");
  if (storedUser) {
    isLoggedIn.value = true;
  } else {
    isLoggedIn.value = false;
  }
});
</script>
