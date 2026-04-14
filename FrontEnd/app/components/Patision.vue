<script setup>
import { computed } from "vue"

const props = defineProps({
  currentPage: { type: Number, required: true },
  totalPages: { type: Number, required: true },
})

const emit = defineEmits(["page-change"])

function goToPage(page) {
  if (page >= 1 && page <= props.totalPages) {
    emit("page-change", page)
  }
}

const pages = computed(() => {
  return Array.from({ length: props.totalPages }, (_, i) => i + 1)
})
</script>

<template>
  <div class="d-flex justify-content-between align-items-center mt-3">
    <div>
      Trang {{ props.currentPage }} / {{ props.totalPages }}
    </div>
    <div class="btn-group">
      <button
        class="btn btn-outline-primary btn-sm"
        :disabled="props.currentPage === 1"
        @click="goToPage(props.currentPage - 1)"
      >
        « Trước
      </button>

      <button
        v-for="page in pages"
        :key="page"
        class="btn btn-sm"
        :class="page === props.currentPage ? 'btn-primary' : 'btn-outline-primary'"
        @click="goToPage(page)"
      >
        {{ page }}
      </button>

      <button
        class="btn btn-outline-primary btn-sm"
        :disabled="props.currentPage === props.totalPages"
        @click="goToPage(props.currentPage + 1)"
      >
        Sau »
      </button>
    </div>
  </div>
</template>
