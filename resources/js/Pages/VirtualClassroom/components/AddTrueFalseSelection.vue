<template>
    <div style="width: 100%">
        <div style="width: 100%">
            <table class="table table-bordered">
                <thead style="position: sticky">
                    <tr>
                        <th class="text-center">
                            <small><strong>Alternativa</strong></small>
                        </th>
                        <th class="text-center">
                            <small><strong>✔</strong></small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Fila de opciones existentes -->
                    <tr v-for="(opcion, index) in opciones" :key="index">
                        <td>
                            <div class="edit-text">
                                <textarea
                                    class="form-control"
                                    v-model="opcion.descripcion"
                                    placeholder="Alternativa"
                                    rows="1"
                                    disabled
                                    style="
                                        resize: none;
                                        overflow: hidden;
                                        width: 100%;
                                    "
                                ></textarea>
                            </div>
                        </td>
                        <td class="text-center">
                            <input
                                type="checkbox"
                                :checked="opcion.correcta == 'S' ? true : false"
                                disabled
                                class="check-correcta"
                            />
                        </td>
                    </tr>
                    <!-- Última fila siempre visible para agregar nuevas opciones -->
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    name: "AddSimpleOption",
    props: {
        value: Array, // v-model para enlazar el array de opciones con el padre
        save_mode: {
            type: Boolean,
            default: false,
        },
        validar_correctas: {
            type: Boolean,
            default: false,
        },
        validar_cantidad_alternativas: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            newOption: {
                descripcion: "",
                porcentaje: "0.00",
                correcta: false,
            },
        };
    },
    computed: {
        opciones: {
            get() {
                return this.value;
            },
            set(newValue) {
                this.$emit("input", newValue);
            },
        },
    },
    methods: {},
};
</script>

<style scoped>
/* Tus estilos aquí */
textarea {
    font-size: 13px;
}
.edit-text {
    display: flex;
}

.check-correcta:disabled {
    background-color: #28a745;
    border-color: #28a745;
    cursor: not-allowed;
}
</style>
