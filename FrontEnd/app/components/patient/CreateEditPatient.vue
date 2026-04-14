<template>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="mb-4 fw-bold">
          {{ mode === "create" ? "Đăng kí tài khoản" : "Cập nhật tài khoản" }}
        </h5>

        <Form @submit="handleSubmit" :initial-values="form" :validation-schema="schema">
          <!-- Name & BHYT -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="bhyt" class="form-label">Số thẻ bảo hiểm</label>
              <Field name="bhyt" as="input" v-model="form.bhyt" type="text" class="form-control" />
              <ErrorMessage name="bhyt" class="text-danger" />
            </div>
            <div class="col-md-6">
              <label for="name" class="form-label">Họ Tên</label>
              <Field name="name" as="input" v-model="form.name" type="text" class="form-control" />
              <ErrorMessage name="name" class="text-danger" />
            </div>
          </div>

          <!-- Address, Date of birth, Gender -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="address" class="form-label">Địa chỉ</label>
              <Field name="address" as="input" v-model="form.address" type="text" class="form-control" />
              <ErrorMessage name="address" class="text-danger" />
            </div>
            <div class="col-md-3">
              <label for="date" class="form-label">Ngày sinh</label>
              <Field name="date" as="input" v-model="form.date" type="date" class="form-control" />
              <ErrorMessage name="date" class="text-danger" />
            </div>
            <div class="col-md-3">
              <label for="gender" class="form-label">Giới tính</label>
              <Field name="gender" as="select" v-model="form.gender" class="form-select">
                <option value="">-- Chọn --</option>
                <option value="Nam">Nam</option>
                <option value="Nữ">Nữ</option>
              </Field>
              <ErrorMessage name="gender" class="text-danger" />
            </div>
          </div>

          <!-- CCCD -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="cccd" class="form-label">CCCD</label>
              <Field name="cccd" as="input" v-model="form.cccd" type="text" class="form-control" />
              <ErrorMessage name="cccd" class="text-danger" />
            </div>
          </div>

          <!-- Gmail & Password -->
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="gmail" class="form-label">Gmail</label>
              <Field name="gmail" as="input" v-model="form.gmail" type="email" class="form-control" :disabled="mode === 'update'" />
              <ErrorMessage name="gmail" class="text-danger" />
            </div>
            <div class="col-md-6" v-if="mode === 'create'">
              <label for="password" class="form-label">Mật khẩu</label>
              <Field name="password" as="input" v-model="form.password" type="password" class="form-control" />
              <ErrorMessage name="password" class="text-danger" />
            </div>
          </div>

          <!-- Prehistoric medical info -->
          <div class="mb-3">
            <label for="prehistoric" class="form-label">Tiền sử bệnh</label>
            <Field name="prehistoric" as="textarea" v-model="form.prehistoric" class="form-control" rows="3" />
            <ErrorMessage name="prehistoric" class="text-danger" />
          </div>

          <!-- Submit button -->
          <div class="text-center">
            <button type="submit" class="btn btn-primary px-4">
              {{ mode === "create" ? "Đăng ký" : "Cập nhật" }}
            </button>
          </div>
        </Form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, onMounted, inject, defineProps } from "vue"
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import { useRouter } from "vue-router"
import { APISERVER } from "~/const/api"

const router = useRouter()
const alert = inject("alert")
const confirm = inject("confirm")

/**
 * Define props for create/update mode and patient id
 * @param {String} mode - "create" | "update"
 * @param {String | Number} id - Patient id
 */
const props = defineProps({
  mode: { type: String, required: true }, // "create" | "update"
  id: { type: [String, Number], default: null }
})

// reactive form state
const form = reactive({
  name: "",
  gender: "",
  date: "",
  address: "",
  gmail: "",
  cccd: "",
  bhyt: "",
  password: "",
  prehistoric: ""
})

/**
 * Fetch patient data when mode is 'update'
 */
onMounted(async () => {
  if (props.mode === "update" && props.id) {
    const token = localStorage.getItem("patient_token")
    try {
      const { data } = await useApi(`/patient/${props.id}`, {
        headers: { Authorization: `Bearer ${token}` },
        method: "GET"
      })
      Object.assign(form, {
        ...data.data,
        date: data.data.date ? data.data.date.substring(0, 10) : ""
      })
    } catch (err) {
      alert(err.message, "danger")
    }
  }
})

/**
 * Handle form submission for create/update
 * @param {Object} values Form values
 * @description Handle form submission for create/update
 */
const handleSubmit = async (values) => {
  const token = localStorage.getItem("patient_token")
  const payload = { ...values }
  if (!payload.password) delete payload.password

  try {
    if (props.mode === "create") {
      const ok = await confirm("Tạo mới tài khoản?", "Bạn có chắc muốn tạo?")
      if (!ok) return
      await $fetch(`${APISERVER}/patient`, {
        method: "POST",
        headers: { Authorization: `Bearer ${token}` },
        body: payload
      })
      alert("Tạo thành công", "success")
      const check = await confirm("Tạo tài khoản thàng công.", "Bạn có muốn đăng nhập luôn không?")
      if(check){
        router.push("/clients/login")
      }else{
        router.push("/clients")
      }

    } else if (props.mode === "update") {
      const ok = await confirm("Cập nhật tài khoản?", "Bạn có chắc muốn cập nhật?")
      if (!ok) return
      await $fetch(`${APISERVER}/patient/${props.id}`, {
        method: "PUT",
        headers: { Authorization: `Bearer ${token}` },
        body: payload
      })
      alert("Cập nhật thành công", "success")
      router.push("/clients")
    }
  } catch (err) {
    alert(err.data?.message || err.message, "danger")
  }
}

/**
 * Form validation schema
 * @returns {yup.ObjectSchema}
 * @description Form validation schema
 */
const schema = yup.object({
  name: yup.string().required("Nhập tên của bạn"),
  gender: yup.string().required("Chọn giới tính"),
  date: yup.date()
    .required("Nhập ngày sinh")
    .max(new Date(), "Ngày sinh phải nhỏ hơn hiện tại"),
  address: yup.string().required("Nhập địa chỉ"),
  gmail: yup.string().email("Email không hợp lệ"),
  cccd: yup.string().matches(/^\d+$/, "CCCD phải là số").length(12, "CCCD 12 số"),
  bhyt: yup.string().matches(/^\d+$/, "BHYT phải là số").length(12, "BHYT 12 số"),
  password: props.mode === "create"
    ? yup.string().required("Mật khẩu không được để trống").min(6, "Ít nhất 6 ký tự")
    : yup.string().nullable(),
  prehistoric: yup.string()
})
</script>
