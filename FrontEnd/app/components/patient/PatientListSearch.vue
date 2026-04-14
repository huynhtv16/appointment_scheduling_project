<template>
<div>
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="d-flex gap-2 w-50">
      <input
        type="text"
        v-model="search"
        class="form-control"
        placeholder="Tìm kiếm theo tên, email, CCCD, BHYT, giới tính, địa chỉ, tiền sử..."
      />
    </div>
  </div>

  <!-- Patient list container -->
  <div class="p-3 mb-3 bg-white border rounded">
    <div class="d-flex align-items-center gap-2 mb-3">
      <h6 class="mb-0 px-2">Danh sách bệnh nhân</h6>
    </div>

    <!-- Loading, error, or table display -->
    <div v-if="loading" class="text-muted">Đang tải dữ liệu...</div>
    <div v-else-if="error" class="text-danger">Lỗi: {{ error }}</div>
    <div v-else class="table-responsive">
      <!-- Patient table -->
      <table class="table table-hover align-middle custom-table">
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Họ và tên</th>
            <th>Ngày sinh</th>
            <th>Giới tính</th>
            <th>Email</th>
            <th>CCCD</th>
            <th>BHYT</th>
            <th>Tiền sử</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <!-- Loop through patients -->
          <tr v-for="patient in patients" :key="patient.id">
            <td><input type="checkbox" /></td>
            <td>{{ patient.id }}</td>
            <td>{{ patient.name }}</td>
            <td>{{ formatDate(patient.date) }}</td>
            <td>{{ patient.gender }}</td>
            <td>{{ patient.gmail }}</td>
            <td>{{ patient.cccd }}</td>
            <td>{{ patient.bhyt }}</td>
            <td>{{ patient.prehistoric }}</td>
            <td>
              <!-- Action buttons -->
              <div class="d-flex gap-1">
                <!-- View patient details -->
                <button 
                  class="btn btn-sm btn-outline-info" 
                  @click="openViewPatient(patient)"
                  title="Xem chi tiết"
                >
                  <i class="bi bi-eye"></i>
                </button>
                <!-- Edit patient -->
                <button 
                  class="btn btn-sm btn-outline-warning" 
                  @click="openEditPatient(patient)"
                  title="Chỉnh sửa"
                >
                  <i class="bi bi-pencil"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination component -->
      <Pagination
        v-if="pagination.total > pagination.per_page"
        :pagination="pagination"
        :page="page"
        @update:page="setPage"
        @change="() => fetchPatients(search)"
      />
    </div>
    <CreateEditPatientAdmin ref="patientModal" @save="handleSave" @success="handleSuccess" />
  </div>
</div>
</template>

<script setup>
import Pagination from './Pagination.vue'
import CreateEditPatientAdmin from './CreateEditPatientAdmin.vue'
import { ref, watch, onMounted, inject } from "vue"
import { APISERVER } from "~/const/api"

// Local state
const search = ref("")
const patients = ref([])
const loading = ref(true)
const error = ref(null)
const pagination = ref({})
const page = ref(1)
const perPage = ref(10)

const alert = inject("alert")

// Reference to patient modal component
const patientModal = ref(null)

// Functions to open modal
const openEditPatient = (patient) => patientModal.value.open(patient, 'edit')
const openViewPatient = (patient) => patientModal.value.open(patient, 'view')

/**
 * Fetch patients from the server
 * @param keyword 
 * @returns {Promise<void>}
 */
const fetchPatients = async (keyword = "") => {
  search.value = keyword || ""
  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem("token")
    if (!token) {
      error.value = "Bạn chưa đăng nhập"
      return
    }

    const res = await fetch(
      `${APISERVER}/patient?page=${page.value}&per_page=${perPage.value}&search=${search.value}`,
      {
        method: "GET",
        headers: {
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      }
    )

    if (!res.ok) throw new Error("Lỗi tải dữ liệu")
    const data = await res.json()
    patients.value = data.data ?? []
    pagination.value = data.meta ?? {}
  } catch (err) {
    error.value = err.message || "Không thể tải dữ liệu"
  } finally {
    loading.value = false
  }
}

/**
 * Set the current page for pagination
 * @param val 
 * @return {void} 
 */
const setPage = (val) => {
  page.value = val
  fetchPatients(search.value)
}

/**
 * Handle saving patient data
 * @param formData 
 * @returns {Promise<void>}
 */
const handleSave = async (formData) => {
  const token = localStorage.getItem("token")
  if (!token) throw new Error("Token không tồn tại")

  const url = formData.id
    ? `${APISERVER}/patient/${formData.id}`
    : `${APISERVER}/patient`
  const method = formData.id ? "PUT" : "POST"

  //eslint-disable no-unused-vars
  const response = await fetch(url, {
    method,
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })

  if (!response.ok) {
    throw new Error("Lỗi khi lưu dữ liệu")
  }

  await fetchPatients(search.value)
}

/**
 * Show success message from child
 * @param message
 * @return {void}
 */
const handleSuccess = (message) => {
  if (alert) alert(message, "success")
}

/* Watch for changes in search input
*/
watch(search, (newValue) => {
  page.value = 1
  fetchPatients(newValue || "")
})

/* Expose search to parent components
*/
defineExpose({ search })

/* Format date to "dd/mm/yyyy"
*/
const formatDate = (dateString) => {
  if (!dateString) return ""
  return new Date(dateString).toLocaleDateString("vi-VN")
}

onMounted(fetchPatients)
</script>
