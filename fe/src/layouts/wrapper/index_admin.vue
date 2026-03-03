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
        </div>
        <BotAdmin></BotAdmin>
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
    margin-left: var(--admin-sidebar-width, 260px);
    margin-top: 0 !important;
    transition: margin-left 0.3s ease-in-out;
    min-height: 100vh;
    overflow-x: hidden;
    padding-left: 16px;
    padding-right: 16px;
    padding-top: 0;
}

/* Override app.css global page-content padding */
.page-content {
    padding: 0 !important;
}

@media (max-width: 991.98px) {
    .page-wrapper {
        margin-left: 0;
        padding-left: 12px;
        padding-right: 12px;
    }
}
</style>
