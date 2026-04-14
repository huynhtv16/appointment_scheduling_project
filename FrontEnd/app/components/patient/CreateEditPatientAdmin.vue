<template>
  <div v-if="visible" class="modal-backdrop">
    <div class="modal-content">
      <h5 class="mb-3">{{ getModalTitle() }}</h5>

      <!-- Form VeeValidate -->
      <Form @submit="handleSubmit" :initial-values="formData" :validation-schema="schema">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Số thẻ bảo hiểm</label>
            <Field name="bhyt" as="input" v-model="formData.bhyt" type="text" class="form-control" 
                   placeholder="Nhập số thẻ bảo hiểm" :disabled="isViewMode" />
            <ErrorMessage name="bhyt" class="text-danger small" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Họ Tên</label>
            <Field name="name" as="input" type="text" v-model="formData.name" class="form-control" 
                   placeholder="Nguyen Van A" :disabled="isViewMode" />
            <ErrorMessage name="name" class="text-danger small" />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Địa chỉ</label>
            <Field name="address" as="input" type="text" v-model="formData.address" class="form-control" 
                   placeholder="Nhập địa chỉ" :disabled="isViewMode" />
            <ErrorMessage name="address" class="text-danger small" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Ngày sinh</label>
            <Field name="date" as="input" type="date" class="form-control" v-model="formData.date" 
                   :disabled="isViewMode" />
            <ErrorMessage name="date" class="text-danger small" />
          </div>
          <div class="col-md-3">
            <label class="form-label">Giới tính</label>
            <Field name="gender" v-model="formData.gender" as="select" class="form-select" 
                   :disabled="isViewMode">
              <option value="">-- Chọn --</option>
              <option value="Nam">Nam</option>
              <option value="Nữ">Nữ</option>
            </Field>
            <ErrorMessage name="gender" class="text-danger small" />
          </div>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">CCCD</label>
            <Field name="cccd" as="input" type="text" v-model="formData.cccd" class="form-control" 
                   placeholder="Nhập số CCCD" :disabled="isViewMode" />
            <ErrorMessage name="cccd" class="text-danger small" />
          </div>
          <div class="col-md-6">
            <label class="form-label">Gmail</label>
            <Field name="gmail" as="input" type="email" v-model="formData.gmail" class="form-control" 
                   placeholder="Nhập Gmail" :disabled="isViewMode || isEditMode" />
            <ErrorMessage name="gmail" class="text-danger small" />
          </div>
        </div>

        <div class="mb-3" v-if="!isViewMode">
          <label class="form-label">Mật khẩu</label>
          <Field name="password" as="input" type="password" v-model="formData.password" 
                 class="form-control" placeholder="Nhập mật khẩu" />
          <ErrorMessage name="password" class="text-danger small" />
        </div>

        <div class="mb-3">
          <label class="form-label">Tiền sử bệnh</label>
          <Field name="prehistoric" as="textarea" v-model="formData.prehistoric" class="form-control" 
                 rows="3" placeholder="Nhập tiền sử bệnh" :disabled="isViewMode" />
          <ErrorMessage name="prehistoric" class="text-danger small" />
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary" @click="close">Đóng</button>
          <button v-if="isViewMode" type="button" class="btn btn-primary" @click="switchToEditMode">Chỉnh sửa</button>
          <button v-else type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </Form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from "vue"
import { Form, Field, ErrorMessage } from "vee-validate"
import * as yup from "yup"


const MASK_PASSWORD = '********'

const visible = ref(false)
const isViewMode = ref(false)
const isEditMode = ref(false)

const emit = defineEmits(["save", "success"])

const formData = reactive({
  id: null,
  bhyt: "",
  name: "",
  address: "",
  date: "",
  gender: "",
  cccd: "",
  gmail: "",
  password: "",
  prehistoric: ""
})

/**
 * Get the modal title based on the current mode
 */
const getModalTitle = () => {
  if (isViewMode.value) return "Chi tiết bệnh nhân"
  return formData.id ? "Sửa thông tin bệnh nhân" : "Thêm bệnh nhân mới"
}

/**
 * Validation schema using Yup
 * @returns {yup.ObjectSchema}
 */
const schema = yup.object().shape({
  id: yup.mixed().nullable(),
  name: yup.string().required("Nhập tên của bạn"),
  gender: yup.string()
    .oneOf(["Nam", "Nữ", ""], "Giới tính không hợp lệ")
    .required("Nhập giới tính của bạn"),
  date: yup.date()
    .required("Nhập Ngày sinh của bạn")
    .transform((curr, orig) => (orig === "" ? null : curr))
    .typeError("Ngày sinh không hợp lệ")
    .max(new Date(), "Ngày sinh phải nhỏ hơn ngày hiện tại"),
  address: yup.string().required("Nhập địa chỉ của bạn"),
  gmail: yup.string().email("Email không hợp lệ"),
  cccd: yup.string()
    .matches(/^\d+$/, "CCCD phải là số")
    .length(12, "CCCD phải có 12 chữ số"),
  bhyt: yup.string()
    .matches(/^\d+$/, "BHYT phải là số")
    .length(12, "BHYT phải 12 chữ số"),
  password: yup.string().when('id', (id, s) => {
    return id
      ? s.required('Vui lòng nhập mật khẩu')
          .test('edit-password-rule', 'Mật khẩu ít nhất 6 ký tự', (val) => val === MASK_PASSWORD || (typeof val === 'string' && val.length >= 6))
      : s.required('Mật khẩu không được để trống').min(6, 'Mật khẩu ít nhất 6 ký tự')
  }),
  prehistoric: yup.string()
})

/**
 * Open the modal for creating a new patient, editing an existing one, or viewing patient details
 * @param patient: Object|null - if provided, prefill the form for editing/viewing
 * @param mode: String - 'create', 'edit', or 'view'
 */
const open = (patient = null, mode = 'create') => {
  isViewMode.value = mode === 'view'
  isEditMode.value = mode === 'edit'
  
  if (patient) {
    Object.assign(formData, {
      ...patient,
      // Show masked password when editing
      password: MASK_PASSWORD,
      date: patient.date ? patient.date.toString().substring(0, 10) : ""
    })
  } else {
    Object.assign(formData, {
      id: null,
      bhyt: "",
      name: "",
      address: "",
      date: "",
      gender: "",
      cccd: "",
      gmail: "",
      password: "",
      prehistoric: ""
    })
  }
  visible.value = true
}

/**
 * Switch from view mode to edit mode
 * @description Switch from view mode to edit mode
 */
const switchToEditMode = () => {
  isViewMode.value = false
  isEditMode.value = true
}

/**
 * Close the modal
 */
const close = () => {
  visible.value = false
  isViewMode.value = false
  isEditMode.value = false
}

/**
 * Submit the form data
 * Emits the "save" event with a copy of formData
 */
const handleSubmit = async () => {
  try {
    const payload = { ...formData }

    if (payload.id && (!payload.password || payload.password === MASK_PASSWORD)) {
      delete payload.password
    }
    await emit("save", payload)
    emit("success", formData.id ? "Cập nhật bệnh nhân thành công!" : "Thêm bệnh nhân thành công!")
    close()
  } catch (error) {
   
    throw error
  }
}

// Expose open method to parent
defineExpose({ open })
</script>
<style src="@/assets/css/CreateEditPatientAdmin.css" scoped></style>

