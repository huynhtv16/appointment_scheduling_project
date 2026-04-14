<template>
  <div class="my-4 mx-0">
    <div class="card shadow-sm border-0">
      <!-- Header + Filter -->
      <div
        class="card-header bg-white border-0 d-flex justify-content-between align-items-center"
      >
        <h6 class="mb-0 fw-bold text-uppercase">Danh sách ca hẹn</h6>
        <div class="d-flex gap-2">
          <!-- Dropdown dịch vụ -->
          <div class="dropdown">
            <button
              class="btn btn-outline-secondary dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
            >
              {{
                selectedService !== null
                  ? SERVICES.find((s) => s.id === selectedService)?.name
                  : "Dịch vụ"
              }}
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="applyFilter(null, selectedStatus)">Tất cả</a>
              </li>
              <li v-for="s in SERVICES" :key="s.id">
                <a class="dropdown-item" @click="applyFilter(s.id, selectedStatus)">
                  {{ s.name }}
                </a>
              </li>
            </ul>
          </div>

          <!-- Dropdown tình trạng -->
          <div class="dropdown">
            <button
              class="btn btn-outline-secondary dropdown-toggle"
              type="button"
              data-bs-toggle="dropdown"
            >
              {{
                selectedStatus !== null
                  ? STATUS.find((st) => st.id === selectedStatus)?.name
                  : "Tình trạng"
              }}
            </button>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" @click="applyFilter(selectedService, null)">Tất cả</a>
              </li>
              <li v-for="st in STATUS" :key="st.id">
                <a class="dropdown-item" @click="applyFilter(selectedService, st.id)">
                  {{ st.name }}
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div class="table">
        <table class="table  mb-0 align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>STT</th>
              <th>Họ và tên</th>
              <th>Tình trạng</th>
              <th>CCCD</th>
              <th>Ngày khám</th>
              <th>Bác sĩ phụ trách</th>
              <th>Dịch vụ</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(appointment, index) in patients"
              :key="appointment.id"
              @dblclick="goToPatient(appointment.patient.id)"
              style="cursor: pointer"
            >
              <td>{{ index + 1 }}</td>
              <td>{{ appointment.patient?.name || "Chưa có" }}</td>

              <!-- Status dropdown -->
              <td>
                <div class="dropdown">
                  <button
                    class="btn btn-sm dropdown-toggle"
                    :class="getStatusClass(appointment.status)"
                    type="button"
                    data-bs-toggle="dropdown"
                  >
                    {{ getStatusName(appointment.status) }}
                  </button>
                  <ul class="dropdown-menu">
                    <li
                      v-for="st in STATUS"
                      :key="st.id"
                      @click="updateStatus(appointment.id, st.id)"
                    >
                      <a class="dropdown-item" href="#">{{ st.name }}</a>
                    </li>
                  </ul>
                </div>
              </td>

              <td>{{ appointment.patient?.cccd || "—" }}</td>
              <td>{{ appointment.date }}</td>

              <!-- Doctor dropdown -->
              <td>
                <div class="dropdown">
                  <button
                    class="btn btn-sm btn-outline-secondary dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                  >
                    {{ appointment.doctor?.name || "Chưa chọn" }}
                  </button>
                  <ul class="dropdown-menu">
                    <li
                      v-for="doctor in doctors"
                      :key="doctor.id"
                      @click="updateDoctor(appointment.id, doctor.id)"
                    >
                      <a class="dropdown-item" href="#">{{ doctor.name }}</a>
                    </li>
                  </ul>
                </div>
              </td>

              <td>{{ getServiceName(appointment.service_id) }}</td>
            </tr>

            <tr v-if="!patients.length">
              <td colspan="8" class="text-muted">Không có dữ liệu</td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="card-footer bg-white border-0">
        <Pagination
          v-if="pagination.total > perPage"
          :pagination="pagination"
          :page="page"
          @update:page="page = $event"
          @change="onPageChange"
        />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, inject } from "vue";
import { useRouter } from "vue-router";
import { STATUS } from "@/const/status";
import { SERVICES } from "@/const/service";
import { APISERVER } from "@/const/api";
import Pagination from "../../patient/Pagination.vue";

const router = useRouter();
const alert = inject("alert");
const doctors = ref([]);
const patients = ref([]);
const selectedService = ref(null);
const selectedStatus = ref(null);
const page = ref(1);
const perPage = ref(10);
const pagination = ref({
  total: 0,
  per_page: 10,
  current_page: 1,
  last_page: 1,
});

/**
 * Navigate to patient detail
 * @param {number|string} idPatient - Patient ID
 * @returns {void}
 */
function goToPatient(idPatient) {
  router.push(`/admin/staff/${idPatient}`);
}

/**
 * Fetch doctors list
 * @param {void}
 * @returns {Promise<void>} Update doctors state
 */
async function getDoctors() {
  try {
    const token = localStorage.getItem("token");
    const res = await fetch(`${APISERVER}/doctors`, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    });
    const data = await res.json();
    if (data.status) {
      doctors.value = data.data;
    }
  } catch (err) {
    console.error("Lỗi khi lấy danh sách bác sĩ:", err);
  }
}

/**
 * Fetch patients with filters & pagination
 * @param {void}
 * @returns {Promise<void>} Update patients & pagination state
 */
async function fetchPatients() {
  try {
    const token = localStorage.getItem("token");

    let url = `${APISERVER}/appointments/staff?page=${page.value}&per_page=${perPage.value}`;
    if (selectedStatus.value !== null) url += `&status=${selectedStatus.value}`;
    if (selectedService.value !== null) url += `&service_id=${selectedService.value}`;

    const res = await fetch(url, {
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
    });

    const data = await res.json();

    if (data.data) {
      const order = { 0: 1, 1: 2, 2: 3, "-1": 4 };
      patients.value = data.data.sort((a, b) => {
        return (order[a.status] || 99) - (order[b.status] || 99);
      });
    }

    pagination.value = data.meta;
  } catch (error) {
    console.error("Error fetching appointments:", error);
  }
}

/**
 * Apply filter by service & status
 * @param {number|null} serviceId - Service ID
 * @param {number|null} statusId - Status ID
 * @returns {void}
 */
function applyFilter(serviceId, statusId) {
  selectedService.value = serviceId;
  selectedStatus.value = statusId;
  page.value = 1; // reset về trang 1
  fetchPatients();
}

/**
 * Update patient status
 * @param {number} patientId - Appointment/Patient ID
 * @param {number} newStatus - New status value
 * @returns {Promise<void>} Update patient status in state
 */
async function updateStatus(patientId, newStatus) {
  if (newStatus == 2) {
    alert(
      "Bạn không thể thay đổi trạng thái thành đã hoàn thành. Vui lòng liên hệ với bác sĩ được chỉ định.",
      "warning"
    );
    return;
  }
  try {
    const token = localStorage.getItem("token");
    const res = await fetch(`${APISERVER}/appointments/${patientId}/status`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ status: newStatus }),
    });
    const data = await res.json();
    if (data.status) {
      const patient = patients.value.find((p) => p.id === patientId);
      if (patient) patient.status = newStatus;
      alert("Cập nhật thành công", "success");
    } else {
      alert(data.error || data.message || "Không thể cập nhật trạng thái", "danger");
    }
  } catch (err) {
    console.error(err);
    alert(err.message, "danger");
  }
}

/**
 * Update doctor for appointment
 * @param {number} appointmentId - Appointment ID
 * @param {number} doctorId - Doctor ID
 * @returns {Promise<void>} Update doctor in state
 */
async function updateDoctor(appointmentId, doctorId) {
  try {
    const token = localStorage.getItem("token");
    const res = await fetch(`${APISERVER}/appointments/${appointmentId}/doctor`, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json",
        Authorization: `Bearer ${token}`,
      },
      body: JSON.stringify({ id_doctor: doctorId }),
    });
    const data = await res.json();
    if (data.status) {
      const appointment = patients.value.find((p) => p.id === appointmentId);
      if (appointment) {
        appointment.id_doctor = doctorId;
        appointment.doctor = doctors.value.find((d) => d.id === doctorId);
      }
      alert("Cập nhật thành công", "success");
    } else {
      alert(data.error || data.message || "Không thể cập nhật bác sĩ", "danger");
    }
  } catch (err) {
    console.error("Lỗi cập nhật bác sĩ:", err);
    alert(err.message || "Lỗi cập nhật bác sĩ", "danger");
  }
}

/**
 * Get status name by ID
 * @param {number} statusId - Status ID
 * @returns {string} Status name in Vietnamese
 */
function getStatusName(statusId) {
  const found = STATUS.find((s) => s.id === statusId);
  return found ? found.name : "Không xác định";
}

/**
 * Get CSS class by status
 * @param {number} statusId - Status ID
 * @returns {string} Bootstrap class
 */
function getStatusClass(statusId) {
  switch (statusId) {
    case 2:
      return "btn-success";
    case 1:
      return "btn-warning";
    case 0:
      return "btn-info";
    case -1:
      return "btn-danger";
    default:
      return "btn-secondary";
  }
}

/**
 * Get service name by ID
 * @param {number} serviceId - Service ID
 * @returns {string} Service name in Vietnamese
 */
function getServiceName(serviceId) {
  const found = SERVICES.find((s) => s.id === serviceId);
  return found ? found.name : "Không xác định";
}

onMounted(() => {
  fetchPatients();
  getDoctors();
});

/**
 * Handle pagination change
 * @param {void}
 * @returns {void}
 */
const onPageChange = () => {
  fetchPatients();
};
</script>

