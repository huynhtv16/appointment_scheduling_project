<template>
  <div class="my-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-0">
        <div class="d-flex justify-content-between align-items-center mb-3 px-3 pt-3">
          <h5 class="mb-0">Lịch hẹn</h5>
          <div class="d-flex gap-2">
            <input type="text" v-model="searchQuery" @input="fetchAppointments" placeholder="Tìm theo tên..."
              class="form-control form-control-sm" style="max-width: 200px" />
            <input type="date" v-model="selectedDate" @change="fetchAppointments" class="form-control form-control-sm"
              style="max-width: 200px" />
          </div>
        </div>

        <table class="table table-striped mb-0 align-middle text-center bordor-top-2">
          <thead class="table-light">
            <tr>
              <th scope="col">STT</th>
              <th scope="col">Họ và tên</th>
              <th scope="col">Thời gian khám</th>
              <th scope="col">Dịch vụ khám</th>
              <th scope="col">CCCD</th>
              <th scope="col">Xác nhận</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in filteredAppointments" :key="item.id">
              <td>{{ (page - 1) * perPage + index + 1 }}.</td>
              <td><b>{{ item.patient?.name || "N/A" }}</b></td>
              <td>{{ formatDate(item.date) }}</td>
              <td>{{ getServiceName(item.service_id) }}</td>
              <td>{{ item.patient?.cccd || "N/A" }}</td>
              <td>
                <button class="btn btn-sm btn-primary" @click="$emit('confirm', item)">
                  Xác nhận khám
                </button>
              </td>
            </tr>
            <tr v-if="!filteredAppointments.length">
              <td colspan="6" class="text-muted">Không có dữ liệu</td>
            </tr>
          </tbody>
        </table>

        <div class="mt-3 px-3" v-if="pagination.total > perPage">
          <Pagination :pagination="pagination" :page="page" @update:page="onPageChange" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue"
import { SERVICES } from "~/const/service"
import { APISERVER } from "~/const/api"
import Pagination from "../../patient/Pagination.vue"

const appointments = ref([])   
const selectedDate = ref(new Date().toISOString().substring(0, 10)) 
const searchQuery = ref("")    
const page = ref(1)            
const perPage = ref(10)        
const pagination = ref({
  total: 0,
  current_page: 1,
  last_page: 1,
  per_page: 10,
})

defineEmits(["confirm"]) 

/**
 * Fetch appointments with pagination and search
 * @param {void}
 * @returns {Promise<void>} Update appointments & pagination state
 */
async function fetchAppointments() {
  try {
    const doctor = JSON.parse(localStorage.getItem("user"))
    const token = localStorage.getItem("token")

    const res = await fetch(
      `${APISERVER}/appointments/today/${doctor.id}?date=${selectedDate.value}&search=${searchQuery.value}&page=${page.value}&per_page=${perPage.value}`,
      {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
        },
      }
    )

    const data = await res.json()
    if (Array.isArray(data.data)) {
      appointments.value = data.data
      pagination.value = data.meta
    } else {
      appointments.value = []
      pagination.value = { total: 0, current_page: 1, last_page: 1, per_page: perPage.value }
    }
  } catch (error) {
    console.error("Error fetching appointments:", error)
    appointments.value = []
    pagination.value = { total: 0, current_page: 1, last_page: 1, per_page: perPage.value }
  }
}

/**
 * Filter appointments by patient name
 * @param {void}
 * @returns {Array} Filtered appointments
 */
const filteredAppointments = computed(() => {
  if (!searchQuery.value) return appointments.value
  return appointments.value.filter(item =>
    item.patient?.name?.toLowerCase().includes(searchQuery.value.toLowerCase())
  )
})

/**
 * Get service name by ID
 * @param {number} serviceId 
 * @returns {string} 
 */
function getServiceName(serviceId) {
  const found = SERVICES.find(s => s.id === serviceId)
  return found ? found.name : "Không xác định"
}

/**
 * Format datetime to localized string
 * @param {string} datetime 
 * @returns {string} 
 */
function formatDate(datetime) {
  if (!datetime) return "N/A"
  return new Date(datetime).toLocaleString("vi-VN")
}

/**
 * Handle page change
 * @param {number} newPage 
 * @returns {void}
 */
const onPageChange = (newPage) => {
  page.value = newPage
  fetchAppointments()
}

onMounted(fetchAppointments)
</script>

