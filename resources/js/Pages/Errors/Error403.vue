<script setup>
import {onMounted,defineProps,ref} from 'vue';
import LayoutError from "../../Layouts/LayoutError.vue";
import {routeTo} from "../../helpers";
const props=defineProps({
    message:String
})
onMounted(() => {
    document.title='403 not found'
})
const image=ref(`background-image: url(${routeTo('images/403.svg')})`)
</script>

<template>
    <LayoutError>
        <template #code>403</template>
        <template #message>{{props.message}}</template>
        <template #image>
            <div :style=image class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
            </div>
        </template>
    </LayoutError>
</template>

<style scoped>

</style>
