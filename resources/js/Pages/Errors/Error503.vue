<script setup>
import {onMounted,ref} from 'vue';
import LayoutError from "../../Layouts/LayoutError.vue";
import {routeTo} from "../../helpers";
onMounted(() => {
    document.title='503 maintenance mode'
})
const image=ref(`background-image: url(${routeTo('images/503.svg')})`)
</script>

<template>
    <LayoutError>
        <template #code>503</template>
        <template #message>Lo sentimos, estamos realizando mantenimiento</template>
        <template #image>
            <div :style=image class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
            </div>
        </template>
    </LayoutError>
</template>

<style scoped>

</style>

