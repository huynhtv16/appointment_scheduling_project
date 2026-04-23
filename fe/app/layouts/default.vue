<template>
  <div class="bg-light">
    <!-- Confirm modal component -->
    <ConfirmModal ref="confirmModal" />
    <!-- Alert component -->
    <Alert ref="alertModel"/>
    
    <div class="container-fluid bg-main">
      <div class="row">
        <!-- Header component -->
        <AppHeader />
      </div>
      
      <div class="row main-menu">
        <!-- Sidebar -->
        <div class="col-12 col-xl-2 col-lg-3 p-0">
          <Sidebar />
        </div>

        <!-- Main content area -->
        <div class="col-12 col-xl-10 col-lg-9 p-0">
          <div class="container-fluid">
            <!-- Slot for page content -->
            <div class="p-4 min-vh-100">
              <slot></slot>
            </div>

            <!-- Footer -->
            <div class="row">
              <AppFooter />
            </div>  
          </div>         
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import AppHeader from '~/components/user/Header.vue'
import AppFooter from '~/components/Footer.vue';
import { Sidebar } from '#components';
import ConfirmModal from '~/components/ConfirmDialog.vue';
import Alert from '~/components/Alert.vue'

// References to modal components
const confirmModal = ref(null)
const alertModel = ref(null)

/**
 * Provide global confirm function
 * @param {string} title - Title of the confirm dialog
 * @param {string} message - Message in the confirm dialog
 * @returns {Promise<boolean>} - Resolves when user confirms or cancels
 */
provide('confirm', (title, message) => confirmModal.value.open(title, message))

/**
 * Provide global alert function
 * @param {string} message - Message to display
 * @param {string} type - Type of alert ('success', 'danger', 'warning', etc.)
 */
provide('alert', (message, type) => alertModel.value.open(message,type))
</script>

<style scoped>
/* Main background color and full height */
.bg-main {
  background-color: #f3f3f3; 
  min-height: 100vh;
}

/* Utility class for min-height */
.min-vh-100 {
  min-height: 100vh;
}
</style>
