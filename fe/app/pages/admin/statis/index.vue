<script setup>
import { onMounted, inject } from 'vue'
import Statis from '@/components/statis/statis.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const alert = inject('alert')

/* Check if user is admin on mount */
onMounted(() => {
  const user = JSON.parse(localStorage.getItem('user') || '{}')
  if(user.id_role !== 1){
    if (alert) alert('Bạn không có quyền truy cập')
    router.push('/admin')
  }
})
</script>


<template>
  <Statis />
  
</template>
