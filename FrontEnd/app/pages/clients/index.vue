<template>
  <div>
    <landing></landing>
  </div>
</template>

<script setup>
    definePageMeta({
        layout: 'patient',
    });
    import landing from '../../components/patient/landing.vue';
</script>
