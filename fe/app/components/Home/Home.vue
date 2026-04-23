<template>
  <!-- Row thống kê -->
  <div class="row g-3 mb-4">
    <div class="col-lg-4">
      <div class="card stat-card purple">
        <div class="card-body">
          <h5 class="card-title">
            <span v-if="loading">...</span>
            <span v-else>{{ stats.total }}</span>
          </h5>
          <p class="card-text">Lịch hẹn đã đặt</p>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card stat-card green">
        <div class="card-body">
          <h5 class="card-title">
            <span v-if="loading">...</span>
            <span v-else>{{ stats.success }}</span>
          </h5>
          <p class="card-text">Lịch hẹn thành công</p>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card stat-card orange">
        <div class="card-body">
          <h5 class="card-title">
            <span v-if="loading">...</span>
            <span v-else>{{ stats.failed }}</span>
          </h5>
          <p class="card-text">Lịch hẹn thất bại</p>
        </div>
      </div>
    </div>
  </div>

<div class="card mb-4">
  <div class="card-header">TOP 5 DỊCH VỤ ĐƯỢC ĐẶT NHIỀU NHẤT TRONG NGÀY</div>
  <div class="card-body">
    <!-- Loading -->
    <div v-if="loading" class="text-center text-muted py-3">
      <div class="spinner-border text-primary" role="status"></div>
      <p class="mt-2">Đang tải dữ liệu...</p>
    </div>

    <!-- Table -->
    <div v-else class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>STT</th>
            <th>Tên dịch vụ</th>
            <th>Giá</th>
            <th>Ngày đặt</th>
            <th>Tổng số đơn đặt</th>
            <th>Thành công</th>
            <th>Không thành công</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="!topServices.length">
            <td colspan="7" class="text-center text-muted">(Chưa có dữ liệu)</td>
          </tr>
          <tr v-for="(service, index) in topServices" :key="index">
            <td>{{ index + 1 }}</td>
            <td>{{ service.name }}</td>
            <td>{{ service.price.toLocaleString() }} đ</td>
            <td>{{ service.date }}</td>
            <td>{{ service.total }}</td>
            <td>{{ service.success }}</td>
            <td>{{ service.failed }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


  <div class="card">
    <div class="card-header">TOP BÁC SĨ CÓ NHIỀU LỊCH HẸN NHẤT</div>
    <div class="card-body">
      <div v-if="loading" class="text-center text-muted py-3">
        <div class="spinner-border text-danger" role="status"></div>
        <p class="mt-2">Đang tải dữ liệu...</p>
      </div>

      <canvas v-else id="doctorChart"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, nextTick, onMounted, inject } from "vue"
import { Chart, registerables } from "chart.js"
import { SERVICES } from "../../const/service"
import { APISERVER } from "../../const/api"

// Register all Chart.js components
Chart.register(...registerables)

// Emits
const emit = defineEmits(["error"])

// Local state
const loading = ref(true)
const stats = ref({ total: 0, success: 0, failed: 0 })
const topServices = ref([])
const topDoctors = ref([])
const alert = inject("alert")

// Store chart instance to destroy before re-rendering
let doctorChartInstance = null

// Watch topDoctors data, render chart when data changes and not loading
watch(
  () => topDoctors.value,
  async (newVal) => {
    if (!loading.value && newVal.length) {
      await nextTick()
      renderDoctorChart()
    }
  },
  { deep: true }
)

/**
 * Render bar chart for top doctors
 */
function renderDoctorChart() {
  const ctx = document.getElementById("doctorChart")
  if (!ctx) return

  // Destroy previous chart instance if exists
  if (doctorChartInstance) doctorChartInstance.destroy()

  // Create new chart
  doctorChartInstance = new Chart(ctx, {
    type: "bar",
    data: {
      labels: topDoctors.value.map(d => d.name),
      datasets: [
        {
          label: "Tổng lịch hẹn",
          data: topDoctors.value.map(d => d.total),
          borderRadius: 8,
          borderSkipped: false
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true } }
    }
  })
}

// Fetch helper
const fetchWithToken = (url, options = {}) => {
  const token = localStorage.getItem("token")
  if (!token) throw new Error("Token không tồn tại")
  return $fetch(url, {
    ...options,
    headers: {
      Authorization: `Bearer ${token}`,
      ...(options.headers || {})
    }
  })
}

// Load data on mount
onMounted(async () => {
  loading.value = true
  try {
    const [totalRes, successRes, failedRes] = await Promise.all([
      fetchWithToken(`${APISERVER}/appointments/total`),
      fetchWithToken(`${APISERVER}/appointments/success`),
      fetchWithToken(`${APISERVER}/appointments/failed`)
    ])

    stats.value = {
      total: totalRes.total ?? 0,
      success: successRes.success ?? 0,
      failed: failedRes.failed ?? 0
    }

    const servicesRes = await fetchWithToken(
      `${APISERVER}/appointments/topSymptoms?date=${new Date().toISOString().slice(0, 10)}`
    )

    topServices.value = servicesRes.map((item, index) => {
      const service = SERVICES.find(s => s.id === item.id_service)
      return {
        id: index + 1,
        name: service ? service.name : "Không rõ",
        price: service ? service.price : 0,
        date: item.booking_date ?? "",
        total: item.total ?? 0,
        success: item.success ?? 0,
        failed: item.failed ?? 0
      }
    })

    const doctorsRes = await fetchWithToken(
      `${APISERVER}/appointments/topDoctors?date=${new Date().toISOString().slice(0, 10)}`
    )

    topDoctors.value = doctorsRes
      .filter(item => item.doctor && item.doctor.name)
      .map(item => ({
        id: item.id_doctor,
        name: item.doctor.name,
        total: item.total ?? 0
      }))
  } catch (error) {
    console.error("Lỗi khi tải dữ liệu:", error)
    if (alert) alert(error.message || "Không thể tải dữ liệu", "danger")
    emit("error", error.message || "Không thể tải dữ liệu")
  } finally {
    loading.value = false
  }
})
</script>

<style src="@/assets/css/Home.css"></style>
