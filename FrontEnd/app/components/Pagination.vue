<script setup>
const { meta } = defineProps({
  meta: {
    type: Object,
    default: () => ({ current_page: 1, last_page: 1, per_page: 10, total: 0 })
  }
})

defineEmits(["page-change"])
</script>

<template>
  <div v-if="meta && meta.total > 0" class="flex items-center justify-center gap-2 mt-4">
    <button
      :disabled="meta.current_page === 1"
      @click="$emit('page-change', meta.current_page - 1)"
      class="px-3 py-1 border rounded disabled:opacity-50"
    >
      Prev
    </button>

    <span>Trang {{ meta.current_page }} / {{ meta.last_page }}</span>

    <button
      :disabled="meta.current_page === meta.last_page"
      @click="$emit('page-change', meta.current_page + 1)"
      class="px-3 py-1 border rounded disabled:opacity-50"
    >
      Next
    </button>
  </div>
</template>
