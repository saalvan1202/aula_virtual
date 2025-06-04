<template>
    <div style="position: relative; cursor: pointer">
        <div @click="handleClickIcon">
            <span>
                <feather-icon icon="MoreVerticalIcon"></feather-icon>
            </span>
        </div>
        <div v-if="activeDropdown" class="dd-menu" @click.stop>
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        activeDropdown: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        handleClickIcon() {
            this.$emit("toggle");
        },
    },
};
</script>

<style scoped>
.dd-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e2e2e2;
    border-radius: 5px;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 100; /* Incrementa el z-index */
    min-width: 200px;
    padding: 10px;
    display: block; /* Asegúrate de que no esté oculto */
}

.dd-menu p {
    margin: 0;
    padding: 5px 10px;
    cursor: pointer;
}

.dd-menu p:hover {
    background: #f5f5f5;
}
</style>
