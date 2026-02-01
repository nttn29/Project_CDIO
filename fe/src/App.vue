<template>
  <component :is="layout">
    <router-view />
  </component>
</template>

<script>
import index_client from './layouts/wrapper/index_client.vue';
import index_admin from './layouts/wrapper/index_admin.vue';
export default {
  components: {
    index_client, index_admin
  },
  computed: {
    layout() {
      const layout = this.$route.meta.layout;
      if (layout === "Admin") return index_admin;
      return index_client;
    }
  }
};
</script>
