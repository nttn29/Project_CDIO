<template>
    <div class="wrapper">
        <div class="header-wrapper">
            <Top></Top>
            <MenuAdmin></MenuAdmin>
        </div>
        <div class="page-wrapper">
            <div class="page-content">
                <router-view></router-view>
            </div>
            <BotAdmin></BotAdmin>
        </div>
    </div>
</template>
<script>
import Top from '../compoments/Top.vue'
import MenuAdmin from '../compoments/MenuAdmin.vue';
import BotAdmin from '../compoments/BotAdmin.vue';
export default {
    name: "app",
    components: {
        Top, MenuAdmin, BotAdmin
    }
}
</script>
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

/* Ensure main page content sits below the fixed header (70px) */
.page-wrapper {
    padding-top: 80px;
}
</style>