<template>
  <div v-if="loaded" class="card shadow-sm border-0 rounded-3">
    <div class="card-body">
      <form @submit.prevent="submitDiagnosis">
        <!-- Hiển thị thông tin bệnh nhân -->
        <div class="row mb-3">
          <div class="col-md-6">
            <label>Số thẻ bảo hiểm y tế</label>
            <input class="form-control" :value="appointment.patient.bhyt" disabled />
          </div>
          <div class="col-md-6">
            <label>Họ tên</label>
            <input class="form-control" :value="appointment.patient.name" disabled />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Địa chỉ</label>
            <input class="form-control" :value="appointment.patient.address" disabled />
          </div>
          <div class="col-md-3">
            <label>Ngày sinh</label>
            <input type="date" class="form-control" :value="formatDate(appointment.patient.date)" disabled />
          </div>
          <div class="col-md-3">
            <label>Giới tính</label>
            <input class="form-control" :value="appointment.patient.gender" disabled />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label>Dịch vụ khám</label>
            <input class="form-control" :value="getServiceName(appointment.service_id)" disabled />
          </div>
        </div>

        <!-- Form chẩn đoán -->
        <div class="row mb-3">
          <div class="col-12">
            <label>Chuẩn đoán</label>
            <textarea class="form-control" rows="4" v-model="diagnosis" placeholder="Nhập chuẩn đoán"></textarea>
          </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-center gap-2 mt-4">
          <button type="submit" class="btn btn-primary">Xác nhận</button>
          <button type="button" class="btn btn-danger" @click="cancel">Hủy</button>
        </div>
      </form>
    </div>
  </div>

  <div v-else class="text-center my-5">
    <p>Đang tải dữ liệu...</p>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { SERVICES } from '~/const/service'
import { APISERVER } from '~/const/api'

const router = useRouter()
const route = useRoute()
const loaded = ref(false)                
const appointment = ref(null)           
const diagnosis = ref('')                
const appointmentId = route.params.id
const alert = inject("alert")

/**
 * Fetch appointment and patient info on mount
 * @async
 * @returns {Promise<void>}
 */
onMounted(async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await fetch(`${APISERVER}/appointments/${appointmentId}/patient-info`, {
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`
      }
    })   
    const data = await res.json()
    if (data.status) {
      appointment.value = data.data
      diagnosis.value = data.data.diagnosis || ''
      loaded.value = true
    } else {
      alert('Không tìm thấy appointment') 
      router.back()
    }
  } catch (error) {
    console.error(error)
    alert('Lỗi tải dữ liệu') 
    router.back()
  }
})

/**
 * Submit updated diagnosis
 * @async
 * @returns {Promise<void>}
 */
const submitDiagnosis = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await fetch(`${APISERVER}/examinations/${appointmentId}/diagnosis`, {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json',
        "Authorization": `Bearer ${token}`
      },
      body: JSON.stringify({ diagnosis: diagnosis.value })
    })
    const data = await res.json()
    if (data.status) {
      alert('Lưu chuẩn đoán thành công')
      router.back()
    } else {
      alert('Lưu thất bại') 
    }
  } catch (error) {
    console.error(error)
    alert('Lỗi lưu chuẩn đoán') 
  }
}

const cancel = () => router.back()

/** Get service name by ID 
 * @param {number} serviceId 
 * @returns {string}
 */
function getServiceName(serviceId) {
  const found = SERVICES.find(s => s.id === serviceId)
  return found ? found.name : "Chưa xác định"
}

/** Format datetime string to YYYY-MM-DD
 * @param {string} dateStr 
 * @returns {string}
 */
function formatDate(dateStr) {
  if (!dateStr) return ''
  return dateStr.split('T')[0]
}
</script>
