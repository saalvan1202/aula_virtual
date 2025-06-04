<template>
    <div class="main-menu menu-fixed menu-accordion menu-shadow menu-light">
        <!-- main menu header-->
        <div class="navbar-header expanded">
            <ul class="nav navbar-nav flex-row">
                <!-- Logo & Text -->
                <li class="nav-item mr-auto">
                    <a class="navbar-brand">
                        <span class="brand-logo">
                            <img
                                :src="$page.props.imagenes.logo"
                                style="max-height: 50px; max-width: 100%"
                                alt="logo"
                            />
                        </span>
                        <h2 class="brand-text">{{ appName }}</h2>
                    </a>
                </li>
                <!-- Toggler Button -->
                <li class="nav-item nav-toggle">
                    <a class="nav-link modern-nav-toggle">
                        <feather-icon
                            icon="XIcon"
                            size="20"
                            class="d-block d-xl-none"
                            @click="toggleVerticalMenuActive"
                        />
                        <feather-icon
                            icon="DiscIcon"
                            size="20"
                            class="d-none d-xl-block collapse-toggle-icon"
                            @click="toggleCollapsed"
                        />
                    </a>
                </li>
            </ul>
        </div>
        <!-- / main menu header-->
        <div
            :class="{ 'd-block': shallShadowBottom }"
            class="shadow-bottom"
        ></div>
        <!-- main menu content-->
        <VuePerfectScrollbar
            :settings="{
                maxScrollbarLength: 60,
                wheelPropagation: false,
            }"
            class="main-menu-content scroll-area"
            style="padding-top: 1em"
            tagname="ul"
            @ps-scroll-y="
                (evt) => {
                    this.shallShadowBottom = evt.srcElement.scrollTop > 0;
                }
            "
        >
            <VerticalMainMenu
                class="navigation navigation-main"
                :menu-items="navMenuItems"
                id="main-menu-navigation"
            />
        </VuePerfectScrollbar>
        <!-- /main menu content-->
    </div>
</template>

<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import VerticalMainMenu from "./Menu/VerticalMainMenu";
export default {
    name: "VerticalNavMenu",
    components: {
        VuePerfectScrollbar,
        VerticalMainMenu,
    },
    props: {
        isVerticalMenuActive: Boolean,
        toggleVerticalMenuActive: Function,
    },
    data() {
        return {
            shallShadowBottom: false,
        };
    },
    methods: {
        toggleCollapsed() {
            console.log("toggle collapsed");
        },
    },
    computed: {
        navMenuItems() {
            return this.$page.props.menus || [];
        },
        appName() {
            return '';
            //return this.$page.props.app_name || "";
        },
    },
};
</script>

<style scoped>
.brand-text {
    color: #00793d !important;
}
</style>
