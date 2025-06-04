<template>
    <div class="vertical-layout h-100"
         :class="[layoutClasses]"
    >
        <NavBar
            :toggle-vertical-menu-active="toggleVerticalMenuActive"
            :navbar-type="navbarTypeV"
        />
        <VerticalNavMenu
            :toggle-vertical-menu-active="toggleVerticalMenuActive"
            :is-vertical-menu-active="isVerticalMenuActive"
        />
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

<script setup>
import {ref,onUnmounted} from "vue";
import NavBar from "../../Components/NavBar.vue";
import VerticalNavMenu from "../../Components/Vertical/VerticalNavMenu";
import AppContent from "../../AppContent";
import useVerticalLayout from '../../../vuexy/useVerticalLayout';
const navbarTypeV=ref('sticky')
const footerTypeV=ref('static')
const {
    isVerticalMenuActive,
    currentBreakpoint,
    layoutClasses,
    overlayClasses,
    resizeHandler,
    toggleVerticalMenuActive,
}=useVerticalLayout(navbarTypeV,footerTypeV);
resizeHandler()
window.addEventListener('resize', resizeHandler)
onUnmounted(() => {
    window.removeEventListener('resize', resizeHandler)
})
</script>

<style scoped>

</style>
