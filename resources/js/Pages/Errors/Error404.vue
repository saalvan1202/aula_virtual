<script setup>
import {onMounted,ref} from 'vue';
import LayoutError from "../../Layouts/LayoutError.vue";
import {routeTo} from "../../helpers";
onMounted(() => {
    document.title='404 not found'
})
const image=ref(`background-image: url(${routeTo('images/404.svg')})`)
</script>

<template>
    <LayoutError>
        <template #code>404</template>
        <template #message>Lo sentimos, pero la página que estás buscando no ha sido encontrada. Intente verificar la URL para ver si hay errores</template>
        <template #image>
            <div :style=image class="absolute pin bg-cover bg-no-repeat md:bg-left lg:bg-center">
            </div>
        </template>
    </LayoutError>
</template>

<style scoped>

</style>
