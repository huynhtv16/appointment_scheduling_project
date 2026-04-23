<script setup>
import { ref, watch, onMounted, computed, inject } from 'vue'
import { Chart, registerables } from 'chart.js'
import axios from 'axios'
import { APISERVER } from "@/const/api.js"

// Register all Chart.js components
Chart.register(...registerables)

// Local state
const from = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0,10))
const to = ref(new Date().toISOString().slice(0,10))
const totalAppointments = ref([])
const doctorStats = ref([])
const loading = ref(false)
const alert = inject('alert')

// Helpers
const formatVNDate = (d) => new Date(d).toLocaleDateString('vi-VN')

const currentDate = computed(() => {
  return new Date().toLocaleDateString('vi-VN')
})

// Chart instances
let totalChart = null
let doctorChart = null

/**
 * Render total appointments line chart
 */
const renderTotalChart = () => {
  const ctx = document.getElementById('totalChart')?.getContext('2d')
  if (!ctx) return
  if (totalChart) totalChart.destroy()

  totalChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: totalAppointments.value.map(i => formatVNDate(i.day)),
      datasets: [{
        label: 'Tổng số lịch',
        data: totalAppointments.value.map(i => i.total),
        borderColor: '#0d6efd',
        backgroundColor: 'rgba(13, 110, 253, 0.1)',
        borderWidth: 2,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#0d6efd',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  })
}

/**
 * Render doctor appointments bar chart
 */
const renderDoctorChart = () => {
  const ctx = document.getElementById('doctorChart')?.getContext('2d')
  if (!ctx) return
  if (doctorChart) doctorChart.destroy()

  doctorChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: doctorStats.value.map(d => d.doctor?.name ?? 'Không rõ'),
      datasets: [{
        label: 'Số lịch hẹn hôm nay',
        data: doctorStats.value.map(d => d.total),
        backgroundColor: '#198754',
        borderColor: '#198754',
        borderWidth: 1,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'top'
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            stepSize: 1
          }
        }
      }
    }
  })
}

/**
 * Watch data and render charts on changes
 */
watch(() => totalAppointments.value, renderTotalChart, { deep: true })
watch(() => doctorStats.value, renderDoctorChart, { deep: true })

// Fetch functions
const fetchDoctorStats = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get(`${APISERVER}/appointments/getDoctorAppointmentStatsToday`, {
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`
      }
    })
    doctorStats.value = res.data.data
  } catch (error) {
    console.error(error)
    if (alert) alert('Không thể tải thống kê bác sĩ', 'danger')
  }
}

/**
 * Fetch stats by date range
 */
const fetchStats = async () => {
  try {
    const token = localStorage.getItem('token')
    const res = await axios.get(`${APISERVER}/appointments/getTotalAppointmentsByRange`, {
      headers: {
        "Content-Type": "application/json",
        "Authorization": `Bearer ${token}`
      },
      params: { from: from.value, to: to.value }
    })
    totalAppointments.value = res.data
  } catch (error) {
    console.error(error)
    if (alert) alert('Không thể tải thống kê theo khoảng ngày', 'danger')
  }
}

onMounted(() => {
  renderTotalChart()
  renderDoctorChart()
  fetchStats()
  fetchDoctorStats()
})
</script>


<template>
  <div class="row g-4">
    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4">Tổng số lịch hẹn theo khoảng ngày</h5>
          
          <div class="row g-3 mb-4 align-items-end">
            <div class="col-sm-6 col-md-3">
              <label class="form-label small text-muted">Từ ngày</label>
              <input type="date" class="form-control" v-model="from" @change="fetchStats()" />
            </div>
            <div class="col-sm-6 col-md-3">
              <label class="form-label small text-muted">Đến ngày</label>
              <input type="date" class="form-control" v-model="to" @change="fetchStats()" />
            </div>
            <div class="col-sm-12 col-md-6 d-flex justify-content-end">
              <button class="btn btn-primary" @click="fetchStats">
                <i class="bi bi-graph-up me-2"></i>Thống kê
              </button>
            </div>
          </div>

          <div class="chart-container">
            <canvas id="totalChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5 class="card-title mb-4">Tỷ lệ lịch hẹn của từng Bác sĩ hôm nay</h5>
          
          <div class="row g-3 mb-4 align-items-center">
            <div class="col-sm-12 col-md-8">
              <div class="text-muted">
                <i class="bi bi-calendar-date me-2"></i>
                Thống kê tổng số lịch hẹn của các bác sĩ trong ngày <strong>{{ currentDate }}</strong>
              </div>
            </div>
            <div class="col-sm-12 col-md-4 d-flex justify-content-end">
              <button class="btn btn-success " @click="fetchDoctorStats">
                <i class="bi bi-arrow-clockwise me-2"></i>Làm mới
              </button>
            </div>
          </div>
          
          <div class="chart-container">
            <canvas id="doctorChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>


<style scoped>
@import "@/assets/css/statis.css";
</style>
