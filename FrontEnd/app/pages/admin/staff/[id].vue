<template>
  <div class="py-4">
    <div class="row">
      <div class="col-lg-7 mb-4">
        <PatientInfo />
      </div>

      <div class="col-lg-5 mb-4 p-0">
        <History />
      </div>
    </div>

    <div class="text-center mt-4">
      <button 
        @click="router.back()" 
        class="btn btn-lg px-5" 
        style="background-color:#4e2cff; color:white;">
        Quay lại
      </button>
    </div>
  </div>
</template>

<script setup>
import PatientInfo from '@/components/user/doctor/PatientInfor.vue'
import History from '@/components/patient/History.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
</script>
