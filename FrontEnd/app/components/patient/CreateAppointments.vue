<template>
  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <div class="card shadow-sm p-4">
          <h5 class="fw-bold mb-4">Đăng kí khám bệnh</h5>

          <Form @submit="handleSubmit" :validation-schema="schema">
            <!-- Patient information -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Số thẻ bảo hiểm</label>
                <input class="form-control" disabled :value="userInfo.bhyt" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Họ Tên</label>
                <input class="form-control" :value="userInfo.name" disabled />
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Địa chỉ</label>
                <input class="form-control" :value="userInfo.address" disabled />
              </div>
              <div class="col-md-3">
                <label class="form-label">Năm sinh</label>
                <input type="number" :value="new Date(userInfo.date).getFullYear()" disabled class="form-control" />
              </div>
              <div class="col-md-3">
                <label class="form-label">Giới tính</label>
                <input type="text" :value="userInfo.gender" disabled class="form-control" />
              </div>
            </div>

            <!-- Symptoms -->
            <div class="mb-3">
              <label class="form-label">Triệu chứng</label>
              <Field name="symptom" as="textarea" rows="3" class="form-control" placeholder="Triệu chứng" />
              <ErrorMessage name="symptom" class="text-danger" />
            </div>

            <!-- Services -->
            <div class="mb-3">
              <label class="form-label">Dịch vụ khám</label>
              <Field name="service" as="select" class="form-select">
                <option value="">--Chọn dịch vụ--</option>
                <option v-for="service in servicesList" :key="service.id" :value="service.id">
                  {{ service.name }} - {{ service.price.toLocaleString() }} VND
                </option>
              </Field>
              <ErrorMessage name="service" class="text-danger" />
            </div>

            <!-- Doctor selection -->
            <div class="mb-3">
              <label class="form-label">Chỉ định bác sĩ (nếu cần)</label>
              <Field name="doctor" as="select" class="form-select" v-model="form.doctor">
                <option value="">Không yêu cầu</option>
                <option v-for="doctor in doctors" :key="doctor.id" :value="doctor.id">
                  {{ doctor.name }}
                </option>
              </Field>
              <ErrorMessage name="doctor" class="text-danger" />
            </div>

            <!-- Date and time -->
            <div class="row mb-4">
              <div class="col-md-6">
                <label class="form-label">Ngày khám</label>
                <Field name="examDate" as="input" type="date" class="form-control"
                  v-model="form.examDate" @change="loadAvailableTimes" />
                <ErrorMessage name="examDate" class="text-danger" />
              </div>
              <div class="col-md-6">
                <label class="form-label">Giờ khám</label>
                <Field name="examTime" as="select" class="form-select" v-model="form.examTime">
                  <option value="">-- Chọn giờ khám --</option>
                  <option v-for="time in availableTimes" :key="time" :value="time">
                    {{ time }}
                  </option>
                </Field>
                <ErrorMessage name="examTime" class="text-danger" />
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary px-5">Đăng ký</button>
            </div>
          </Form>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4">
        <div class="card shadow-sm p-3 mb-3">
          <h6 class="fw-bold mb-3">Danh sách bác sĩ hàng đầu yêu thích</h6>
          <table class="table table-sm table-bordered">
            <tbody>
              <tr><td>Nguyễn Văn A</td><td>Chỉnh hình</td></tr>
              <tr><td>Nguyễn Văn B</td><td>Phẫu thuật</td></tr>
            </tbody>
          </table>
        </div>
        <div class="card shadow-sm p-3">
          <h6 class="fw-bold mb-3">Chi tiết giá dịch vụ</h6>
          <table class="table table-sm table-bordered">
            <tbody>
              <tr><td>Khám sức khỏe</td><td>120.000</td></tr>
              <tr><td>Khám thai</td><td>500.000</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import { reactive, onMounted, ref, inject, watch } from "vue"
import { SERVICES } from "~/const/service"
import { APISERVER } from "~/const/api"
import { useRouter } from "vue-router"

// inject global alert function
const alert = inject("alert")
const router = useRouter()

// constants
const servicesList = SERVICES

// reactive states
const userInfo = reactive({ name:"", gender:"", date:"", address:"", bhyt:"" })
const doctors = ref([])
const id = ref(null)
const form = reactive({ examDate:"", doctor:"" })
const availableTimes = ref([])

// generate available time slots
/**
 * Generate time slots for appointments
 *
 * @returns {Array<String>} Array of time strings like "08:00"
 */
const generateTimeSlots = () => {
  const slots = []
  for (let h=8; h<=11; h++) slots.push(`${String(h).padStart(2,"0")}:00`)
  for (let h=13; h<=17; h++) slots.push(`${String(h).padStart(2,"0")}:00`)
  return slots
}

/**
 * Load available times for selected doctor and date
 */
const loadAvailableTimes = async () => {
  if (!form.examDate) { availableTimes.value = []; return }
  if (!form.doctor) { availableTimes.value = generateTimeSlots(); return }
  try {
    const token = localStorage.getItem("patient_token")
    const { data: resData } = await useApi(
      `/appointments/doctor/${form.doctor}?date=${form.examDate}`,
      { method:"GET", headers:{ Authorization:`Bearer ${token}` } }
    )
    const booked = resData.data.map(appt => appt.date.split(" ")[1].slice(0,5))
    const slots = generateTimeSlots()
    availableTimes.value = slots.filter(t => !booked.includes(t))
  } catch (err) {
    alert(err.message,"danger")
  }
}

// form validation schema
const schema = yup.object({
  examDate: yup.date().required("Chọn ngày khám")
    .min(new Date(new Date().setHours(0,0,0,0)), "Ngày khám phải từ hôm nay trở đi"),
  examTime: yup.string().required("Chọn giờ khám")
    .test("is-future", "Thời gian phải lớn hơn hiện tại", function(value){
      const { examDate } = this.parent
      if (!examDate || !value) return true
      const [h,m] = value.split(":").map(Number)
      const examDateTime = new Date(examDate); examDateTime.setHours(h,m,0,0)
      return examDateTime > new Date()
    }),
  service: yup.string().required("Chọn dịch vụ"),
  symptom: yup.string().required("Mô tả triệu chứng"),
  doctor: yup.string().nullable()
})

// on mount, fetch doctors and patient ID
onMounted(async () => {
  const storedUser = localStorage.getItem("patient")
  if (storedUser) id.value = JSON.parse(storedUser).id

  const token = localStorage.getItem("patient_token")
  // fetch doctors
  const { data: resDoctor } = await useApi(`/users/role/2`, {
    method:"GET", headers:{ Authorization:`Bearer ${token}` }
  })
  doctors.value = resDoctor.data
})

// watch patient ID -> load patient info
watch(id, async (newId) => {
  if (!newId) return
  try {
    const token = localStorage.getItem("patient_token")
    const { data: resData } = await useApi(`/patient/${newId}`, {
      method:"GET", headers:{ Authorization:`Bearer ${token}` }
    })
    Object.assign(userInfo, { ...resData.data, date: resData.data.date?.split(" ")[0] || "" })
  } catch (err) {
    alert(err.message,"danger")
  }
}, { immediate:true })

/**
 * Submit a new appointment
 *
 * @param {Object} values Form values
 */
const handleSubmit = async (values) => {
  const storedUser = JSON.parse(localStorage.getItem("patient") || "{}")
  const token = localStorage.getItem("patient_token")
  const payload = {
    id_patient: storedUser.id,
    id_doctor: values.doctor || null,
    id_service: values.service,
    date: `${values.examDate} ${values.examTime}:00`,
    symptom: values.symptom,
    status: 0
  }
  try {
    await $fetch(`${APISERVER}/appointments/new`, {
      method:"POST",
      headers:{ Authorization:`Bearer ${token}` },
      body: payload
    })
    alert("Tạo thành công","success")
    router.push("/clients/history")
  } catch (err) {
    const message = err.errors || err.data?.message || err.message
    alert(message,"danger")
  }
}
</script>
