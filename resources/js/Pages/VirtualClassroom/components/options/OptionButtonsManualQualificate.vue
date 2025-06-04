<template>
    <div class="container-buttons">
        <b-button
            class="btn btn-primary btn-sm"
            v-b-tooltip.hover
            title="Calificar respuesta"
        >
            <feather-icon icon="EditIcon" @click="handleEdit" />
        </b-button>
    </div>
</template>
<script>
export default {
    name: "OptionButtonsManualQualificate",
    methods: {
        handleEdit() {
            this.$emit("edit");
        },
    },
};
</script>

<style scoped>
.container-buttons {
    display: flex;
    gap: 4px;
}

.btn-alternativas {
    position: relative;

    .alert-icon {
        position: absolute;
        top: -10px;
        /* left: -10px; */
        right: -10px;

        border: 1px solid #b71c1c; /* Rojo oscuro para el borde */
        border-radius: 100%;
        background-color: #d32f2f; /* Rojo intenso */

        padding: 0px 6px;

        display: flex;
        justify-content: center;
        align-items: center;

        animation: blink 2s infinite alternate; /* Alterna cada 1s */
    }
}

@keyframes blink {
    0% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
    50% {
        background-color: #f44336; /* Rojo más brillante (fondo) */
        border-color: #d32f2f; /* Rojo intenso (borde) */
        color: white; /* Texto blanco cuando el fondo se hace más brillante */
    }
    100% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
}
</style>
