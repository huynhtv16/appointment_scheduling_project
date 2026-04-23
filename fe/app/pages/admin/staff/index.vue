<template>
  <div v-if="!isLoggedIn" class="d-flex align-items-center justify-content-center min-vh-100">
    <div>
      <Nologin />
    </div>
  </div>
      <StaffTable />
</template>


<script setup>
import { ref, onMounted } from "vue";
import Nologin from "~/components/NoLogin.vue";
import StaffTable from "@/components/user/doctor/StaffTable.vue";

const isLoggedIn = ref(false);

onMounted(() => {
  const storedUser = localStorage.getItem("user");
  isLoggedIn.value = !!storedUser;
});
</script>
