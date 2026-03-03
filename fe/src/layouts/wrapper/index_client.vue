<template>
  <div class="resident-layout">
    <div class="main">
      <TopClient />

      <div class="content">
        <router-view />
      </div>

      <BotClient />
    </div>
  </div>
</template>

<script setup>
import TopClient from '@/layouts/compoments/TopClient.vue'
import BotClient from '@/layouts/compoments/BotClient.vue'
import "../../assets/js/bootstrap.bundle.min.js";
import "../../assets/js/jquery.min.js";
import "../../assets/plugins/simplebar/js/simplebar.min.js";
import "../../assets/plugins/metismenu/js/metisMenu.min.js";
import "../../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js";
import "../../assets/js/app.js";
import "../../assets/js/pace.min.js";
</script>

<style scoped>
.resident-layout {
  display: flex;
  min-height: 100vh;
}

.main {
  flex: 1;
  display: flex;
  flex-direction: column;
  padding-top: 72px;
}

.content {
  flex: 1;
  padding: 24px;
  /* subtle pale pink -> mainly white to avoid strong pink patches */
  background: linear-gradient(135deg, #fff9fb, #ffffff);
}

/* Card-style wrapper for inner pages: keep content on a soft blue-white panel */
.content .page {
  max-width: 1000px;
  margin: 24px auto;
  background: #f7fbff;
  border-radius: 0;
  padding: 24px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.06);
}

/* Remove rounded corners from all card elements inside client layout */
::v-deep .card {
  border-radius: 0 !important;
}
</style>

<style>
@import "../../assets/plugins/simplebar/css/simplebar.css";
@import "../../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css";
@import "../../assets/plugins/metismenu/css/metisMenu.min.css";
@import "../../assets/css/pace.min.css";
@import "../../assets/css/bootstrap.min.css";
@import "../../assets/css/bootstrap-extended.css";
@import url("https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap");
@import "../../assets/css/app.css";
@import "../../assets/css/icons.css";
@import "../../assets/css/dark-theme.css";
@import "../../assets/css/semi-dark.css";
@import "../../assets/css/header-colors.css";
@import url("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css");
</style>
