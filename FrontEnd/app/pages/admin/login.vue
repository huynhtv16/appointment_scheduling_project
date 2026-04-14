<script setup>
import Login from '~/components/Login.vue';

    definePageMeta({
        layout: 'single',
    });

</script>

<template>
  <div class="d-flex align-items-center justify-content-center min-vh-100" style="background: linear-gradient(90deg, #fffce6, #fffce6);">
    <div class="container">
      <div class="row justify-content-between align-items-center">    
        <div class="col-md-6 text-center">
          <img src="../../assets/images/login.png" alt="doctor" class="img-fluid rounded-3 shadow" />
        </div>
            <Login 
                mode="admin"
            />
      </div>
    </div>
  </div>
</template>
