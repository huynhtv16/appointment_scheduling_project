<template>
    <div 
      v-if="show" 
      class="position-fixed top-50 start-50 translate-middle bg-opacity-30 d-flex justify-content-center align-items-center z-100 "
    >

    <div class="container-fluid bg-white rounded-lg shadow-lg w-full max-w-lg p-3">

      <button @click="close" class="position-absolute text-xxl border-0 bg-white end-0 top-0 m-2">
        &times;
      </button>

      <h2 class="text-xl font-bold mb-4 p-3">Chi tiết khám bệnh</h2>

  
      <div v-if="loading" class="text-center text-gray-500">Chưa có dữ liệu</div>
      <div v-else-if="error" class="text-center text-red-500">{{ error }}</div>
      <div v-else>
        <div class="row m-0">
          <div class="row m-0">
            <label class="m-0 p-0 block text-sm font-medium text-gray-700"><b>Bệnh nhân</b></label>
          </div>
          <div class="row m-0">
            <input type="text" v-model="form.patient" readonly class="mt-1 form-control w-full" />
          </div>
        </div>

        <div class="row m-0">
          <div class="col-6">
            <div class="row m-0"><label><b>CCCD</b></label></div>
            <div class="row m-0"><input type="text" v-model="form.cccd" readonly class="mt-1 form-control w-full" /></div>
          </div>
          <div class="col-3">
            <div class="row m-0"><label><b>Giới tính</b></label></div>
            <div class="row m-0"><input type="text" v-model="form.gender" readonly class="mt-1 form-control w-full" /></div>
          </div>
          <div class="col-3">
            <div class="row m-0"><label><b>Năm sinh</b></label></div>
            <div class="row m-0"><input type="text" v-model="form.birth" readonly class="mt-1 form-control w-full" /></div>
          </div>
        </div>

        <div class="row m-0">
          <div class="row m-0"><label><b>Bác sĩ</b></label></div>
          <div class="row m-0"><input type="text" v-model="form.doctor" readonly class="mt-1 form-control w-full" /></div>
        </div>

        <div class="row m-0">
          <div class="col-6">
            <div class="row m-0"><label><b>Thời gian</b></label></div>
            <div class="row m-0"><input type="text" v-model="form.time" readonly class="mt-1 form-control w-full" /></div>
          </div>
          <div class="col-6">
            <div class="row m-0"><label><b>Dịch vụ</b></label></div>
            <div class="row m-0"><input type="text" :value="getServiceName(form.method)" readonly class="mt-1 form-control w-full" /></div>
          </div>
        </div>

        <div class="row m-0">
          <div class="row m-0"><label><b>Chẩn đoán</b></label></div>
          <div class="row m-0"><textarea v-model="form.diagnosis" readonly class="mt-1 form-control w-full h-24"></textarea></div>
        </div>

        <div class="mt-4 text-right row d-flex justify-content-center">
          <button @click="close" class=" buton-detail-his px-4 py-2 bg-primary text-white border-0 rounded-1">
            Đóng
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
// Imports
import { onMounted, ref, watch } from 'vue'
import { APISERVER } from '../../const/api'
import { SERVICES } from '~/const/service'

// Props from parent
const props = defineProps({
  show: Boolean,  
  id: Number 
})
const emit = defineEmits(['close']) 
const token = ref(null) 

// On component mount, get token from localStorage
onMounted(() => {
  token.value = localStorage.getItem("patient_token") || localStorage.getItem("token")
})


const loading = ref(false)   
const error = ref(null)      
const form = ref({
  patient: '',  
  cccd: '',     
  gender: '',   
  birth: '',    
  doctor: '',   
  time: '',     
  method: '',   
  diagnosis: '' 
})

/**
 * Format datetime to DD/MM/YYYY HH:mm
 * @param {string|Date} value - datetime string or Date object
 * @returns {string} formatted datetime or empty string
 */
function formatDateTime(value) {
  if (!value) return ''
  const d = new Date(value)
  if (isNaN(d)) return value
  const pad = n => (n < 10 ? '0' + n : n)
  return `${pad(d.getDate())}/${pad(d.getMonth() + 1)}/${d.getFullYear()} ${pad(d.getHours())}:${pad(d.getMinutes())}`
}

/**
 * Get service name by ID
 * @param {number|string} serviceId
 * @returns {string} 
 */
function getServiceName(serviceId) {
  const found = SERVICES.find(s => s.id === serviceId)
  return found ? found.name : "Chưa xác định"
}

/**
 * Watch examination ID prop and load data when it changes
 */
watch(
  () => props.id,
  async (id) => {
    form.value = null 
    if (!id) return

    loading.value = true   
    error.value = null   
    try {
      
      const res = await fetch(`${APISERVER}/examinations/${id}`, {
        headers: {
          Authorization: `Bearer ${token.value}`,
          "Content-Type": "application/json"
        },
      })
      const json = await res.json()

      if (!json.status) {
        error.value = json.message || 'Không tải được dữ liệu'
        return
      }

      const data = json.data
      console.log(data)

      // Map API response to form fields
      form.value = {
        patient: data.appointment?.patient?.name || '',
        cccd: data.appointment?.patient?.cccd || '',
        gender: data.appointment?.patient?.gender || '',
        birth: data.appointment?.patient?.date?.split('T')[0] || '',
        doctor: data.appointment?.doctor?.name || '',
        time: formatDateTime(data.examination_time),
        method: data.appointment?.id_service || '',
        diagnosis: data.diagnosis || ''
      }
    } catch (e) {
      error.value = 'Chưa có dữ liệu'
    } finally {
      loading.value = false 
    }
  },
  { immediate: true }  
)

/**
 * Close the modal and emit close event
 */
const close = () => emit('close')
</script>


<style>
label{
  padding: 10px 0px 0px 0px !important;
}
.buton-detail-his{
 width: 110px;
}
</style>
