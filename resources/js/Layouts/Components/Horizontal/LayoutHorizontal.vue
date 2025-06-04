<template>
    <div class="horizontal-layout"
        :class="[layoutClasses]"
        style="height:inherit"
    >
        <BNavbar
            :toggleable="false"
            class="header-navbar navbar-shadow align-items-center navbar-brand-center navbar-fixed"
            :class="{'fixed-top': currentBreakpoint!== 'xl'}"

        >
            <NavBarHorizontal :toggleVerticalMenuActive="toggleVerticalMenuActive"/>
        </BNavbar>
        <!--<NavBar
            :toggle-vertical-menu-active="toggleVerticalMenuActive"
            :navbar-type="navbarType"
        />-->
        <div class="horizontal-menu-wrapper">
            <div
                class="header-navbar navbar-expand-sm navbar navbar-horizontal navbar-light navbar-shadow menu-border d-none d-xl-block"
                :class="[navbarMenuTypeClass]"
            >
                <HorizontalNavMenu />
            </div>
            <VerticalNavMenu
                :toggle-vertical-menu-active="toggleVerticalMenuActive"
                :is-vertical-menu-active="isVerticalMenuActive"
                class="d-block d-xl-none"
            />
        </div>
        <!-- Vertical Nav Menu Overlay -->
        <div class="sidenav-overlay"
             @click="isVerticalMenuActive = false"
             :class="overlayClasses"
        >
        </div>
        <AppContent>
            <template #breadcrumb="slotProps">
                <slot
                name="content-breadcrumb"
                v-bind="slotProps"
                />
            </template>
            <slot/>
        </AppContent>
    </div>
</template>
<script>
import {ref,onUnmounted} from 'vue';
import { BNavbar} from 'bootstrap-vue'
import NavBarHorizontal from './NavBarHorizontal.vue';
import HorizontalNavMenu from './HorizontalNavMenu.vue';
import VerticalNavMenu from '../Vertical/VerticalNavMenu.vue';
import AppContent from '../../AppContent.vue';
import useVerticalLayout from '../../../vuexy/useVerticalLayout';
import useHorizontalLayout from '../../../vuexy/useHorizontalLayout';
export default{
    name:'LayoutHorizontal',
    components:{BNavbar,NavBarHorizontal,HorizontalNavMenu,VerticalNavMenu,AppContent},
    setup(){
        const navbarType=ref('sticky')
        const footerType=ref('static')
        const {
            isVerticalMenuActive,
            currentBreakpoint,
            overlayClasses,
            resizeHandler,
            toggleVerticalMenuActive,
        }=useVerticalLayout(navbarType,footerType);
        resizeHandler()
        window.addEventListener('resize', resizeHandler)
        onUnmounted(() => {
            window.removeEventListener('resize', resizeHandler)
        })

        const {layoutClasses,navbarMenuTypeClass}=useHorizontalLayout(navbarType,footerType,isVerticalMenuActive,currentBreakpoint)
        return{
            navbarType,
            layoutClasses,
            navbarMenuTypeClass,
            isVerticalMenuActive,
            currentBreakpoint,
            toggleVerticalMenuActive,
            overlayClasses
        }
    }
}
</script>
