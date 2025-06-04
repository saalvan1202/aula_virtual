<template>
    <ul>
        <component
            :is="resolveNavItemComponent(item)"
            v-for="item in props.menuItems"
            :key="item.id || item.title"
            :item="item"
            :routeChanged="routeChanged"
        />
    </ul>
</template>

<script setup>

import { onMounted, provide, ref,defineProps } from 'vue'
import { router } from '@inertiajs/vue2'

import {resolveVerticalNavMenuItemComponent as resolveNavItemComponent } from "../../../../vuexy/utils";
const openGroups=ref([]);
provide('openGroups', openGroups)
const routeChanged=ref('');

const props=defineProps({
    menuItems:Array
})
onMounted(()=>{
    router.on('navigate', (event) => {
        routeChanged.value = event.detail.page.url;
    })
})

</script>

<style scoped>

</style>
