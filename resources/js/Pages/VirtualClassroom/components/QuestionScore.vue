<template>
    <div class="container-score">
        <div class="container-score__pregunta">
            <h5 class="m-0 p-0">{{ label }} {{ index }}:</h5>
            <p class="m-0 p-0">{{ descripcion }}</p>
        </div>
        <div class="container-score__btn">
            <!-- Botón hecho manualmente con tooltip -->
            <div class="tooltip-container">
                <button
                    v-if="active_plus"
                    class="btn-custom btn-sm btn-success"
                    @click.prevent="handleAdd"
                >
                    <span>+</span>
                </button>
                <span v-if="active_plus" class="tooltip">Agregar faltante</span>

                <button
                    v-if="active_subtract"
                    class="btn-custom btn-sm btn-danger"
                    @click.prevent="handleSubtract"
                >
                    <span>-</span>
                </button>
                <span v-if="active_subtract" class="tooltip"
                    >Restar excedente</span
                >

                <button
                    v-if="active_edit"
                    class="btn-custom btn-sm btn-success"
                    @click.prevent="handleEdit"
                >
                    <span>~</span>
                </button>
                <span v-if="active_edit" class="tooltip"
                    >Editar manualmente</span
                >
            </div>

            <button
                class="btn btn-primary btn-sm"
                style="min-width: 60px"
                :disabled="disabled"
                @click.prevent="handleModal"
            >
                <p class="m-0 p-0">{{ puntaje == "0.00" ? "S/A" : puntaje }}</p>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: "QuestionScore",
    props: {
        label: {
            type: String,
            default: "Pregunta",
        },
        index: {
            type: Number,
            required: true,
        },
        puntaje: {
            type: String,
            required: true,
        },
        descripcion: {
            type: String,
            required: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        active_plus: {
            type: Boolean,
            default: false,
        },
        active_subtract: {
            type: Boolean,
            default: false,
        },
        active_edit: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        handleModal() {
            this.$emit("create");
        },
        handleAdd() {
            this.$emit("add");
        },
        handleSubtract() {
            this.$emit("subtract");
        },
        handleEdit() {
            this.$emit("edit");
        },
    },
};
</script>

<style scoped>
.container-score {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    border-bottom: 1px solid #ddd;
}

.container-score__pregunta {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.container-score__pregunta p {
    font-size: 13px;
}

.container-score__btn {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
}

/* Estilo del botón manual */
.btn-custom {
    background-color: #28a745;
    border: none;
    color: white;
    font-size: 16px;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s ease;
}

.btn-custom:hover {
    background-color: #218838;
}

/* Tooltip flotante */
.tooltip-container {
    position: relative;
    display: inline-flex;
    align-items: center;
}

.tooltip {
    visibility: hidden;
    background-color: black;
    color: white;
    text-align: center;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 12px;
    position: absolute;
    bottom: 100%;
    left: 50%;
    transform: translateX(-50%) translateY(-5px);
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
}

/* Flecha del tooltip */
.tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border-width: 5px;
    border-style: solid;
    border-color: black transparent transparent transparent;
}

/* Mostrar tooltip al pasar el mouse */
.tooltip-container:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

.container-score:last-child {
    border-bottom: none;
}
</style>
