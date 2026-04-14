<template>
  <div class="card shadow-sm p-4 h-100">
    <h5 class="fw-bold mb-4">Hồ sơ bệnh nhân</h5>

    <!-- Hiển thị loading -->
    <div v-if="loading" class="text-center my-3">
      <span>Đang tải dữ liệu...</span>
    </div>

    <!-- Form patient -->
    <form v-else-if="patient">
      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Số thẻ bảo hiểm</label>
          <input class="form-control" :value="patient.bhyt" disabled/>
        </div>
        <div class="col-md-6">
          <label class="form-label">Họ Tên</label>
          <input class="form-control" :value="patient.name" disabled />
        </div>
      </div>

      <div class="row mb-3">
        <div class="col-md-6">
          <label class="form-label">Địa chỉ</label>
          <input class="form-control" :value="patient.address" disabled />
        </div>
        <div class="col-md-3">
          <label class="form-label">Năm sinh</label>
          <input class="form-control" :value="formattedDate" disabled />
        </div>
        <div class="col-md-3">
          <label class="form-label">Giới tính</label>
          <input class="form-control" :value="patient.gender" disabled />
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label">CCCD</label>
        <input class="form-control" :value="patient.cccd" disabled />
      </div>

      <div class="mb-3">
        <label class="form-label">Tiền sử bệnh</label>
        <textarea :value="patient.prehistoric" class="form-control" rows="5" disabled></textarea>
      </div>
    </form>

    <!-- Nếu không có dữ liệu -->
    <div v-else class="text-danger">Không tìm thấy bệnh nhân</div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject, computed } from "vue"
import { useRouter, useRoute } from "vue-router"
import { APISERVER } from "@/const/api"

const router = useRouter()
const route = useRoute()
const alert = inject("alert")

const loading = ref(true)        
const patient = ref(null)       

/**
 * Fetch patient data by ID
 * @async
 * @returns {Promise<void>} Updates `patient` ref
 */
onMounted(async () => {
  try {
    const token = localStorage.getItem("token")
    const res = await fetch(`${APISERVER}/patient/${route.params.id}`, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    })

    const data = await res.json()
    if (data.status) {
      patient.value = data.data
    } else if (data.id) {
      patient.value = data
    } else {
      alert?.("Không tìm thấy bệnh nhân", "warning")
      router.back()
    }
  } catch (err) {
    console.error("Lỗi fetch bệnh nhân:", err)
    alert?.("Lỗi tải dữ liệu", "danger")
    router.back()
  } finally {
    loading.value = false
  }
})

/**
 * Format date to YYYY-MM-DD
 * @param {string} dateStr - Original date string
 * @returns {string} Formatted date or empty string
 */
function formatDate(dateStr) {
  if (!dateStr) return ''
  const d = new Date(dateStr)
  const year = d.getFullYear()
  const month = String(d.getMonth() + 1).padStart(2, '0')
  const day = String(d.getDate()).padStart(2, '0')
  return `${year}-${month}-${day}`
}

const formattedDate = computed(() => formatDate(patient.value?.date))
</script>

