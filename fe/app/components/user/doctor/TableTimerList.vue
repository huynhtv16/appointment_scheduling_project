<template>
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
        </tr>
      </thead>
      <tbody>
        <tr v-for="(patient, index) in patients" :key="index">
          <td>{{ index + 1 }}</td>
          <td>{{ patient.patient.name }}</td>
          <td :class="['fw-bold', getStatusClass(patient.status)]">
            {{ getStatusName(patient.status) }}
            <span :class="getStatusClass(patient.status)">●</span>
          </td>

          <td>{{ patient.patient.cccd }}</td>
          <td>{{ formatDate(patient.patient.date) }}</td>
          <td>{{ getServiceName(patient.service_id) }}</td>
        </tr>
        <tr v-if="!patients.length">
          <td colspan="7" class="text-muted">Không có dữ liệu</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { STATUS } from "@/const/status";
import { SERVICES } from "@/const/service";

/* Get the display name of a status by its ID
* @param statusId: number
* @returns string - status name or "Unknown"
*/
function getStatusName(statusId) {
  const found = STATUS.find(s => s.id === statusId);
  return found ? found.name : "Không xác định";
}

/* Get the CSS class for status text based on status ID
* @param statusId: number
* @returns string - CSS class for styling
*/
function getStatusClass(statusId) {
  switch (statusId) {
    case 2: 
      return "text-success";  
    case 1:
    case 0: 
      return "text-warning";  
    case -1: 
      return "text-danger";  
    default:
      return "text-secondary"; 
  }
}

/* Get the service name by its ID
* @param serviceId: number
* @returns string - service name or "Not determined"
*/
function getServiceName(serviceId) {
  const found = SERVICES.find(s => s.id === serviceId);
  return found ? found.name : "Không xác định";
}

/* Format date string to "YYYY-MM-DD"
* @param dateStr: string
* @returns string - formatted date or empty string
*/
function formatDate(dateStr) {
  if (!dateStr) return ''
  return dateStr.split('T')[0] 
}

defineProps({
  patients: {
    type: Array,
    default: () => []
  }
})
</script>

