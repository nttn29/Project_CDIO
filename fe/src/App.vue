<template>
  <template v-if="layout === 'blank'">
    <router-view />
  </template>
  <component v-else :is="layoutComponent">
    <router-view />
  </component>
</template>

<script>
import index_client from './layouts/wrapper/index_client.vue';
import index_admin from './layouts/wrapper/index_admin.vue';
import index_technician from './layouts/wrapper/index_technician.vue';

export default {
  components: {
    index_client, index_admin, index_technician
  },
  computed: {
    layout() {
      return this.$route?.meta?.layout || 'Client';
    },
    layoutComponent() {
      if (this.layout === "Admin") return index_admin;
      if (this.layout === "Technician") return index_technician;
      return index_client;
    }
  }
};
</script>
