<script setup>
import { ref, watch, onMounted, inject } from "vue"
import CreateEditUser from "~/components/user/CreateEditUser.vue"
import { ROLES, getRoleName } from "~/const/roles"
import Pagination from "../patient/Pagination.vue"
import { APISERVER } from "@/const/api.js"

const users = ref([])
const pagination = ref({})
const page = ref(1)
const search = ref("")
const roleFilter = ref("")
const loading = ref(true)
const error = ref(null)
const alert = inject("alert")

const userModal = ref(null)

const openCreateUser = () => userModal.value.open(null, 'create')
const openEditUser = (user) => userModal.value.open(user, 'edit')
const openViewUser = (user) => userModal.value.open(user, 'view')

/**
 * Fetch users
 * @param filters 
 */
const fetchUsers = async (filters = {}) => {
  if (filters.search !== undefined) search.value = filters.search
  if (filters.role !== undefined) roleFilter.value = filters.role

  loading.value = true
  error.value = null
  try {
    const token = localStorage.getItem("token")
    if (!token) throw new Error("Token không tồn tại")

    const query = new URLSearchParams({
      page: page.value,
      search: search.value,
      role: roleFilter.value
    })
    
    /**
     * Fetch users
     */
    const res = await fetch(`${APISERVER}/users?${query.toString()}`, {
      method: "GET",
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "application/json"
      }
    })

    if (!res.ok) throw new Error("Lỗi tải dữ liệu")
    const data = await res.json()
    users.value = data.data ?? []
    pagination.value = data.meta ?? {}
  } catch (err) {
    error.value = err.message || "Không thể tải dữ liệu"
  } finally {
    loading.value = false
  }
}

/**
 * Save user (create/update)
 * @param formData 
 */
const handleSave = async (formData) => {
  const token = localStorage.getItem("token")
  if (!token) throw new Error("Token không tồn tại")

  const url = formData.id
    ? `${APISERVER}/users/${formData.id}`
    : `${APISERVER}/users`
  const method = formData.id ? "PUT" : "POST"

  const response = await fetch(url, {
    method,
    headers: {
      Authorization: `Bearer ${token}`,
      "Content-Type": "application/json",
    },
    body: JSON.stringify(formData),
  })

  if (!response.ok) {
    throw new Error("Lỗi khi lưu dữ liệu")
  }

  await fetchUsers()
}

// Show success message
const handleSuccess = (message) => {
  if (alert) alert(message, "success")
}

// React to search/role filter changes
watch([search, roleFilter], () => {
  page.value = 1
  fetchUsers({ search: search.value, role: roleFilter.value })
})

// Format date
const formatDate = (dateString) => {
  if (!dateString) return ""
  return new Date(dateString).toLocaleDateString("vi-VN")
}

onMounted(() => fetchUsers())
</script>

<template>
<div>
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div class="d-flex gap-2 w-50">
      <input
        type="text"
        v-model="search"
        class="form-control"
        placeholder="Tìm kiếm theo tên, gmail..."
      />
    </div>
    <button class="btn btn-primary" @click="openCreateUser">
      + Thêm
    </button>
  </div>

  <div class="p-3 mb-3 bg-white border rounded">
    <div class="d-flex align-items-center gap-2 mb-3">
      <h6 class="mb-0 px-2">Danh sách người dùng</h6>
      <!-- Dropdown to filter users by role -->
      <select v-model="roleFilter" class="form-select w-auto">
        <option value="">Tất cả</option>
        <option v-for="role in ROLES" :key="role.id" :value="role.id">
          {{ role.name }}
        </option>
      </select>
    </div>

    <div v-if="loading" class="text-muted">Đang tải dữ liệu...</div>
    <div v-else-if="error" class="text-danger">Lỗi: {{ error }}</div>

    <div v-else class="table-responsive">
      <!-- User table -->
      <table class="table table-hover align-middle custom-table">
        <thead>
          <tr>
            <th></th>
            <th>ID</th>
            <th>Họ và tên</th>
            <th>Ngày sinh</th>
            <th>Giới tính</th>
            <th>Email</th>
            <th>Trạng thái</th>
            <th>Chức vụ</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="user in users"
            :key="user.id"
          >
            <td><input type="checkbox" /></td>
            <td>{{ user.id }}</td>
            <td>{{ user.name }}</td>
            <td>{{ formatDate(user.date) }}</td>
            <td>{{ user.gender }}</td>
            <td>{{ user.gmail }}</td>
            <td>
              <span
                class="d-inline-flex align-items-center fw-bold"
                :class="user.status == 1 ? 'text-success' : 'text-danger'"
              >
                <span
                  class="status-dot me-1"
                  :class="user.status === 1 ? 'bg-success' : 'bg-danger'"
                ></span>
                {{ user.status === 1 ? "Hoạt động" : "Khóa" }}
              </span>
            </td>
            <td>{{ getRoleName(user.id_role) }}</td>
            <td>
              <div class="d-flex gap-1">
                <button 
                  class="btn btn-sm btn-outline-info" 
                  @click="openViewUser(user)"
                  title="Xem chi tiết"
                >
                  <i class="bi bi-eye"></i>
                </button>
                <button 
                  class="btn btn-sm btn-outline-warning" 
                  @click="openEditUser(user)"
                  title="Chỉnh sửa"
                >
                  <i class="bi bi-pencil"></i>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <Pagination
        v-if="pagination.total > pagination.per_page"
        :pagination="pagination"
        :page="page"
        @update:page="(p) => { page = p; fetchUsers({ search: search.value, role: roleFilter.value }) }"
        @change="() => fetchUsers({ search: search.value, role: roleFilter.value })"
      />
    </div>
  </div>
  <CreateEditUser ref="userModal" @save="handleSave" @success="handleSuccess" />
</div>  
</template>
<style src="@/assets/css/user.css" scoped></style>
