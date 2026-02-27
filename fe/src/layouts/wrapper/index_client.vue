<template>
  <div class="resident-layout">
    <MenuResident />

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
import MenuResident from '@/layouts/compoments/MenuClient.vue'
import TopClient from '@/layouts/compoments/TopClient.vue'
import BotClient from '@/layouts/compoments/BotClient.vue'
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
