<template>
  <!-- --- Main container --- -->
  <div class="container text-center">
    
    <!-- --- Hero title --- -->
    <h2 class="mb-5" style="font-family: 'Dancing Script', cursive; font-size: 5rem;">
      Bệnh viện khủng long
    </h2>

    <!-- --- Section: First row --- -->
    <div class="row align-items-center mb-5">
      <!-- Image left -->
      <div class="col-md-6 mb-3">
        <img src="../../assets/images/image1.png" alt="Khám bệnh" class="img-fluid rounded shadow"/>
      </div>

      <!-- Text & button right -->
      <div class="col-md-6">
        <p style="font-family: 'Dancing Script', cursive; font-size: 2rem;">
          Bệnh viện vip PRO, uy tín làm lên thương hiệu
        </p>
        <nuxt-link to="/clients/appointment" class="btn btn-info text-white fw-bold px-4 py-2">
          Khám ngay tại đây
        </nuxt-link>
      </div>
    </div>

    <!-- --- Section: Second row --- -->
    <div class="row align-items-center">
      <!-- Text left -->
      <div class="col-md-6 mb-3">
        <p style="font-family: 'Dancing Script', cursive; font-size: 2rem;">
          Đội ngũ bác sĩ siêu uy tín, có kinh nghiệm dày dặn
        </p>
      </div>

      <!-- Image right -->
      <div class="col-md-6">
        <img src="../../assets/images/image2.png" alt="Siêu âm" class="img-fluid rounded shadow"/>
      </div>
    </div>

  </div>
</template>

<script setup>
// Empty script 
</script>

<style>
/* Import custom font  */
@import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;600&display=swap');
</style>
