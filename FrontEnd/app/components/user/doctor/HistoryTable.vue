<template>
<div>
      <!-- --- Search filters --- -->
    <div class="d-flex gap-2 w-50 mb-2">
      <input type="text" v-model="searchQuery"
        @input="() => { pagination.current_page = 1; fetchHistory() }" placeholder="Tìm theo tên..."
        class="form-control" />
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <!-- --- Header: title --- -->
        <div class="d-flex justify-content-between align-items-center mb-3 px-3 pt-3">
          <h5 class="mb-0">Lịch sử bệnh nhân</h5>
        </div>

        <div class="table-responsive">
          <table class="table mb-0 align-middle text-center">
            <thead class="table-light">
              <tr>
                <th>STT</th>
                <th>Họ và tên</th>
                <th>Tình trạng</th>
                <th>CCCD</th>
                <th>Ngày sinh</th>
                <th>Dịch vụ</th>
                <th>Chi tiết</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(patient, index) in patients" :key="patient.id">
                <td>{{ (pagination.current_page - 1) * pagination.per_page + index + 1 }}</td>
                <td>{{ patient.patient?.name || "N/A" }}</td>
                <td :class="['fw-bold', getStatusClass(patient.status)]">
                  {{ getStatusName(patient.status) }}
                  <span :class="getStatusClass(patient.status)">●</span>
                </td>
                <td>{{ patient.patient?.cccd || "N/A" }}</td>
                <td>{{ formatDate(patient.patient?.date) }}</td>
                <td>{{ getServiceName(patient.service_id) }}</td>
                <td>
                  <button class="btn btn-sm btn-outline-primary" @click="openDetail(patient.id)">
                    Chi tiết
                  </button>
                </td>
              </tr>
              <tr v-if="!patients.length">
                <td colspan="7" class="text-muted">Không có dữ liệu</td>
              </tr>
            </tbody>
          </table>
          <div class="pe-3">
            <Pagination v-model:page="pagination.current_page" :pagination="pagination" @change="fetchHistory" />
          </div>
        </div>
      </div>
    </div>

    <DetailHistory :show="showDetail" :id="selectedAppointment" @close="showDetail = false" />
</div>
</template>

<script setup>
import { ref, onMounted } from "vue"
import { STATUS } from "@/const/status"
import { SERVICES } from "@/const/service"
import { APISERVER } from "@/const/api"
import DetailHistory from "../../patient/DetailHistory.vue"
import Pagination from "../../patient/Pagination.vue"

const patients = ref([])               
const showDetail = ref(false)          
const selectedAppointment = ref(null) 
const doctor = ref(null)               
const token = ref(null)    
const pagination = ref({
  total: 0,
  per_page: 10,
  current_page: 1,
  last_page: 1,
})

const searchQuery = ref("")           

/**
 * Fetch appointment history of doctor
 * @param {void}
 * @returns {Promise<void>} update patients & pagination
 */
async function fetchHistory() {
  try {
    if (!doctor.value?.id || !token.value) return

    const params = new URLSearchParams({
      page: pagination.value.current_page,
      per_page: pagination.value.per_page,
      search: searchQuery.value || ""
    })
    const doctorId = doctor.value.id_role === 1 ? 0 : doctor.value.id;
    const res = await fetch(
      `${APISERVER}/appointments/doctor/${doctorId}/history?${params.toString()}`,
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token.value}`,
        },
      }
    )

    const data = await res.json()
      patients.value = data.data
      pagination.value = data.meta
  } catch (err) {
    console.error("Error fetching history:", err)
    patients.value = []
  }
}

/**
 * Get status name by ID
 * @param {number} statusId 
 * @returns {string} Tên trạng thái
 */
function getStatusName(statusId) {
  const found = STATUS.find((s) => s.id === statusId)
  return found ? found.name : "Không xác định"
}

/**
 * Get CSS class for status
 * @param {number} statusId 
 * @returns {string} Class name (Bootstrap color)
 */
function getStatusClass(statusId) {
  switch (statusId) {
    case 2: return "text-success"
    case 1:
    case 0: return "text-warning"
    case -1: return "text-danger"
    default: return "text-secondary"
  }
}

/**
 * Get service name by ID
 * @param {number} serviceId 
 * @returns {string} 
 */
function getServiceName(serviceId) {
  const found = SERVICES.find((s) => s.id === serviceId)
  return found ? found.name : "Không xác định"
}

/**
 * Format date to yyyy-mm-dd
 * @param {string} dateStr 
 * @returns {string} 
 */
function formatDate(dateStr) {
  if (!dateStr) return ""
  return dateStr.split("T")[0]
}

/**
 * Open detail popup
 * @param {number} appointmentId 
 * @returns {void}
 */
function openDetail(appointmentId) {
  selectedAppointment.value = appointmentId
  showDetail.value = true
}

onMounted(() => {
  if (typeof window !== "undefined") { 
    const userStr = localStorage.getItem("user")
    doctor.value = userStr ? JSON.parse(userStr) : null
    token.value = localStorage.getItem("token")
    fetchHistory()
  }
})
</script>
