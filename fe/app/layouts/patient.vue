<template>
  <div class="bg-light">
    <!-- Confirm modal component -->
    <ConfirmModal ref="confirmModal" />
    <!-- Alert component -->
    <Alert ref="alertModel"/>

    <div class="container-fluid bg-main">
      <div class="row">
        <!-- Header component for patient -->
        <AppHeader />
      </div>

      <div class="row main-menu">  
        <div class="container-fluid">
          <!-- Main content area -->
          <div class="row p-4 main-detail mt-5" style="min-height: 87vh !important;">
            <slot></slot>
          </div>

          <!-- Footer component -->
          <div class="row">
            <AppFooter />
          </div>  
        </div>         
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, provide } from 'vue'
import AppHeader from '~/components/patient/HeaderPatient.vue'
import AppFooter from '~/components/Footer.vue'
import ConfirmModal from '~/components/ConfirmDialog.vue'
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
