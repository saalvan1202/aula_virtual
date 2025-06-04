<template>
    <BNavbar
        :toggleable="false"
        class="header-navbar navbar navbar-shadow align-items-center"
        :class="[navbarTypeClass]"
        variant=""
    >
        <div class="navbar-container d-flex content align-items-center">
            <!-- Nav Menu Toggler -->
            <ul class="nav navbar-nav d-xl-none">
                <li class="nav-item">
                    <a
                        class="nav-link"
                        style="color: #ffffff"
                        @click="toggleVerticalMenuActive"
                    >
                        <feather-icon icon="MenuIcon" size="21" />
                    </a>
                </li>
            </ul>
            <!-- Left Col -->
            <div
                class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex"
            ></div>
            <BNavbarNav class="nav align-items-center ml-auto">
                <NotificationDropDown />
            </BNavbarNav>
            <BNavbarNav class="nav align-items-center ml-auto">
                <UserDropDown />
            </BNavbarNav>
        </div>
    </BNavbar>
</template>

<script>
import { BNavbar, BNavbarNav } from "bootstrap-vue";
import UserDropDown from "./UserDropDown";
import NotificationDropDown from "./NotificationDropDown.vue";
export default {
    name: "NavBar",
    components: { BNavbar, BNavbarNav, UserDropDown, NotificationDropDown },
    props: {
        toggleVerticalMenuActive: Function,
        navbarType: String,
    },
    computed: {
        navbarTypeClass() {
            if (this.navbarType === "sticky") return "fixed-top";
            if (this.navbarType === "static") return "navbar-static-top";
            if (this.navbarType === "hidden") return "d-none";
            return "floating-nav";
        },
    },
};
</script>

<style scoped>
.feather:hover {
    fill: white;
}
</style>
