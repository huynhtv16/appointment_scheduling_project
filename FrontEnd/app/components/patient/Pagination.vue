<template>
  <!-- --- Pagination navigation --- -->
  <nav 
    v-if="pagination.total > pagination.per_page" 
    class="d-flex justify-content-end mt-3"
  >
    <ul class="pagination">
      <!-- Previous button -->
      <li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
        <button class="page-link" @click="prevPage">Previous</button>
      </li>

      <!-- Current page info -->
      <li class="page-item disabled">
        <span class="page-link">
          Page {{ pagination.current_page }} / {{ pagination.last_page }}
        </span>
      </li>

      <!-- Next button -->
      <li 
        class="page-item" 
        :class="{ disabled: pagination.current_page === pagination.last_page }"
      >
        <button class="page-link" @click="nextPage">Next</button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
/**
 * Props
 * - pagination: pagination metadata from API
 * - page: current active page
 */
const props = defineProps({
  pagination: { type: Object, required: true },
  page: { type: Number, required: true }
});

// Emits
const emit = defineEmits(["update:page", "change"]);

/**
 * Go to previous page.
 */
const prevPage = () => {
  if (props.page > 1) {
    emit("update:page", props.page - 1);
    emit("change");
  }
};

/**
 * Go to next page.
 */
const nextPage = () => {
  if (props.page < props.pagination.last_page) {
    emit("update:page", props.page + 1);
    emit("change");
  }
};
</script>
