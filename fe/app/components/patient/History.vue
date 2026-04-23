<template>
  <!-- --- Main container --- -->
  <div class="container position-relative">
    <div class="card shadow-sm">
      <div class="card-body p-4">

        <!-- --- Header: title and status filter --- -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="card-title">Lịch sử khám bệnh</h5>

          <div class="d-flex gap-2">
            <!-- Status filter -->
            <select v-model="selectedStatus" class="form-select">
              <option value="">Tất cả trạng thái</option>
              <option v-for="s in statusList" :key="s.id" :value="s.id">
                {{ s.name }}
              </option>
            </select>

            <!-- Date filter -->
            <input 
              type="date" 
              v-model="selectedDate" 
              class="form-control"
            />
          </div>
        </div>

        <!-- --- Loading spinner --- -->
        <div v-if="loading" class="text-center my-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
          </div>
        </div>

        <!-- --- Table for history --- -->
        <div v-else class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="table-light">
              <tr>
                <th scope="col">STT</th>
                <th scope="col">Hình thức khám</th>
                <th scope="col">Bác sĩ</th>
                <th scope="col">Thời gian</th>
                <th scope="col">Trạng thái</th>
                <th scope="col">Thao tác</th>
              </tr>
            </thead>

            <!-- Empty state -->
            <div v-if="history.length < 1" class="w-100 text-center text-success"> 
              Bạn chưa có lịch sử khám bệnh.
            </div>

            <tbody v-else>
              <tr v-for="(appointment, index) in history" :key="appointment.id">
                <td>{{ index + 1 }}</td>
                <td>{{ (servicesList.find(s => s.id === appointment.service_id)?.name) ?? "" }}</td>
                <td>{{ appointment.doctor ? appointment.doctor.name : "Chưa xếp" }}</td>
                <td>{{ appointment.date }}</td>

                <!-- Status with color -->
                <td>
                  <span 
                    :class="[
                      'fw-bold',
                      {
                        'text-warning': appointment.status === 0,
                        'text-primary': appointment.status === 1,
                        'text-success': appointment.status === 2,
                        'text-danger': appointment.status === -1
                      }
                    ]"
                  >
                    {{ statusList.find(s => s.id === appointment.status)?.name || "Không xác định" }}
                    <span 
                      class="dot" 
                      :class="{
                        'bg-warning': appointment.status === 0,
                        'bg-primary': appointment.status === 1,
                        'bg-success': appointment.status === 2,
                        'bg-danger': appointment.status === -1
                      }"
                    ></span>
                  </span>
                </td>

                <!-- Action button -->
                <td>
                  <button class="btn btn-primary btn-sm" @click="openDetail(appointment.id)">
                    Chi Tiết
                  </button>
                </td>
              </tr>
            </tbody>
          </table>

          <!-- Pagination -->
          <Pagination 
            v-if="pagination.total > perPage" 
            :pagination="pagination" 
            :page="page" 
            @update:page="setPage"
            @change="fetchHistory"
          />
        </div>
      </div>
    </div>

    <!-- --- Detail modal --- -->
    <DetailHistory 
      :show="showDetail" 
      :id="selectedAppointment" 
      @close="showDetail = false"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, inject, watch } from "vue";
import { SERVICES } from "~/const/service";
import { STATUS } from "~/const/status";
import DetailHistory from "./DetailHistory.vue";
import { useRoute } from "vue-router";
import Pagination from "./Pagination.vue";

const route = useRoute();
const servicesList = SERVICES;
const statusList = STATUS;
const alert = inject("alert");

const history = ref([]);                
const user = ref(null);               
const showDetail = ref(false);          
const selectedAppointment = ref({});         
const loading = ref(true);              

const selectedStatus = ref("");
const selectedDate = ref("");

const page = ref(1);
const perPage = ref(2);
const pagination = ref({}); 

const setPage = (val) => {
  page.value = val;
};


const openDetail = (id) => {
  selectedAppointment.value = id;
  showDetail.value = true;
};

/**
 * Go to previous page.
 */
const fetchHistory = async () => {
  loading.value = true;
  const token = localStorage.getItem("patient_token");

  try {
    const params = new URLSearchParams({
      page: page.value,
      per_page: perPage.value,
    });

    if (selectedStatus.value !== "") {
      params.append("status", selectedStatus.value);
    }
    if (selectedDate.value) {
      params.append("date", selectedDate.value); 
    }
    console.log(`/appointments/history/${user.value.id}?${params.toString()}`);
    const { data: resData, error } = await useApi(
      `/appointments/history/${user.value.id}?${params.toString()}`, 
      {  
        headers: { Authorization: `Bearer ${token}` },
        method: "GET"
      }
    );

    if (error) {
      alert(error, "danger");
      return;
    }

    history.value = resData.data;
    pagination.value = resData.meta;

  } catch (err) {
    alert(err.message, "danger");
  } finally {
    loading.value = false;
  }
};

//Mounted 
onMounted(() => {
  const storedUser = localStorage.getItem("patient");
  user.value = storedUser ? JSON.parse(storedUser) : { id: route.params.id };
  fetchHistory();
});

//Watch filter 
watch([selectedStatus, selectedDate, page], () => {
  fetchHistory();
});
</script>

