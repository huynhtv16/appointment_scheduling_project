<template>
  <div v-if="visible" class="modal-backdrop">
    <div class="modal-content">
      <h5 class="mb-3">{{ getModalTitle() }}</h5>

      <!-- Form VeeValidate -->
      <VForm :validation-schema="schema" :initial-values="formData" @submit="handleSubmit">
        <!-- Hidden field to expose id to validation schema -->
        <Field name="id" type="hidden" v-model="formData.id" />
        <div class="mb-3">
          <label class="form-label">Họ tên</label>
          <Field name="name" type="text" class="form-control" v-model="formData.name" :disabled="isViewMode" />
          <ErrorMessage name="name" class="text-danger small" />
        </div>

        <div class="mb-3">
          <label class="form-label">Giới tính</label>
          <Field as="select" name="gender" class="form-select" v-model="formData.gender" :disabled="isViewMode">
            <option v-for="(item, index) in GENDERS" :key="index" :value="item.value">
              {{ item.label }}
            </option>
          </Field>
          <ErrorMessage name="gender" class="text-danger small" />
        </div>

        <div class="row">
          <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <Field as="select" name="id_role" class="form-select" v-model="formData.id_role" :disabled="isViewMode">
              <option value="">-- Chọn vai trò --</option>
              <option v-for="r in ROLES" :key="r.id" :value="r.id">
                {{ r.name }}
              </option>
            </Field>
            <ErrorMessage name="id_role" class="text-danger small" />
          </div>

          <div class="col mb-3">
            <label class="form-label">Trạng thái</label>
            <Field as="select" name="status" class="form-select" v-model="formData.status" :disabled="isViewMode">
              <option :value="1">Hoạt động</option>
              <option :value="0">Khóa</option>
            </Field>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Gmail</label>
          <Field name="gmail" type="email" class="form-control" v-model="formData.gmail" :disabled="isViewMode" />
          <ErrorMessage name="gmail" class="text-danger small" />
        </div>

        <div class="mb-3" v-if="!isViewMode">
          <label class="form-label">Mật khẩu</label>
          <Field name="password" type="password" class="form-control" v-model="formData.password" />
          <ErrorMessage name="password" class="text-danger small" />
        </div>

        <div class="mb-3">
          <label class="form-label">Ngày sinh</label>
          <Field name="date" type="date" class="form-control" v-model="formData.date" :disabled="isViewMode" />
          <ErrorMessage name="date" class="text-danger small" />
        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary" @click="close">Đóng</button>
          <button v-if="isViewMode" type="button" class="btn btn-primary" @click="switchToEditMode">Chỉnh sửa</button>
          <button v-else type="submit" class="btn btn-primary">Lưu</button>
        </div>
      </VForm>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"
import { Field, Form as VForm, ErrorMessage } from "vee-validate"
import * as yup from "yup"
import "@/assets/css/CreateEditUser.css"
import { GENDERS } from "@/const/sex.js";
import { ROLES } from '@/const/roles.js'

/**
 * Constant for masking password in edit mode
 */
const MASK_PASSWORD = '********'

// Visibility state of the modal
const visible = ref(false)
const isViewMode = ref(false)

/**
 * Emits events to parent
 */
const emit = defineEmits(["save", "success"])

/* Form data */
const formData = ref({
  id: null,
  name: "",
  gender: "",
  gmail: "",
  password: "",
  id_role: 2,
  status: 1,
  date: ""
})

/**
 * Get the modal title based on the current mode
 */
const getModalTitle = () => {
  if (isViewMode.value) return "Chi tiết người dùng"
  return formData.value.id ? "Sửa người dùng" : "Thêm người dùng"
}

/**
 * Validation schema using Yup
 */
const schema = yup.object({
  id: yup.mixed().nullable(),
  name: yup.string().required("Họ tên bắt buộc"),
  gender: yup.string().required("Vui lòng chọn giới tính"),
  gmail: yup.string().email("Email không hợp lệ").required("Email bắt buộc"),
  password: yup
    .string()
    .when("id", (id, s) => {
      return id
        ? s.required("Vui lòng nhập mật khẩu")
            .test(
              "edit-password-rule",
              "Mật khẩu ít nhất 6 ký tự",
              (val) => val === MASK_PASSWORD || (typeof val === 'string' && val.length >= 6)
            )
        : s.required("Mật khẩu bắt buộc").min(6, "Ít nhất 6 ký tự")
    }),
  id_role: yup.number().nullable().transform((value, originalValue) => {
    return originalValue === "" ? null : value
  })
    .required("Vui lòng chọn chức vụ"),
  status: yup.number(),
  date: yup
    .date()
    .nullable()
    .transform((value, originalValue) => {
      return originalValue === "" ? null : value
    })
    .required("Vui lòng nhập ngày sinh!")
    .max(new Date(), "Ngày sinh không được lớn hơn ngày hiện tại"),
})


/**
 * Open the modal for creating a new user, editing an existing one, or viewing user details
 * @param user: Object|null - if provided, prefill the form for editing/viewing
 * @param mode: String - 'create', 'edit', or 'view'
 */
const open = (user = null, mode = 'create') => {
  isViewMode.value = mode === 'view'
  
  if (user) {
    formData.value = {
      ...user,
      password: MASK_PASSWORD,
      gender: user.gender === "Nam" || user.gender === "Nữ" || user.gender === "Khác"
        ? user.gender
        : "",
      date: user.date ? user.date.toString().substring(0, 10) : ""
    }
  } else {
    formData.value = {
      id: null,
      name: "",
      gender: "",
      gmail: "",
      password: "", // luôn để trống khi tạo mới
      id_role: 2,
      status: 1,
      date: ""
    }
  }
  visible.value = true
}

/**
 * Switch from view mode to edit mode
 */
const switchToEditMode = () => {
  isViewMode.value = false
}



/**
 * Close the modal
 */
const close = () => {
  visible.value = false
  isViewMode.value = false
}

/**
 * Submit the form data
 * Emits the "save" event with a copy of formData
 */
const handleSubmit = async () => {
  const currentUser = JSON.parse(localStorage.getItem("user") || "{}")

  if (formData.value.id === currentUser.id && formData.value.status === 0) {
    if (!confirm("Bạn sắp khóa tài khoản của chính mình. Sau khi lưu thành công bạn sẽ bị đăng xuất. Tiếp tục?")) {
      return
    }
  }

  try {
    const payload = { ...formData.value }
    // If editing and password is unchanged (mask) or empty, do not send password
    if (payload.id && (!payload.password || payload.password === MASK_PASSWORD)) {
      delete payload.password
    }
    await emit("save", payload)
    emit("success", formData.value.id ? "Cập nhật người dùng thành công!" : "Thêm người dùng thành công!")
    close()
  } catch (error) {
    throw error
  }
}
defineExpose({ open, close })
</script>
<style src="@/assets/css/CreateEditUser.css"></style>
