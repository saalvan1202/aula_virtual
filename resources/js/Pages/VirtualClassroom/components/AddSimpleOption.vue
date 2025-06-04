<template>
    <div style="width: 100%">
        <div style="width: 100%; max-height: 400px; overflow-y: scroll">
            <table class="table table-bordered">
                <thead style="position: sticky">
                    <tr>
                        <th class="text-center">
                            <small><strong>Alternativa</strong></small>
                        </th>
                        <th class="text-center">
                            <small><strong>✔</strong></small>
                        </th>
                        <th class="text-center">
                            <small><strong>Opción</strong></small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Fila de opciones existentes -->
                    <tr v-for="(opcion, index) in opciones" :key="index">
                        <td>
                            <textarea
                                v-if="!save_mode"
                                class="form-control"
                                v-model="opcion.descripcion"
                                placeholder="Alternativa"
                                rows="1"
                                style="
                                    resize: none;
                                    overflow: hidden;
                                    width: 100%;
                                "
                                @input="autoExpand($event)"
                            ></textarea>
                            <div class="edit-text" v-else>
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
                                    @input="autoExpand($event)"
                                ></textarea>
                                <button
                                    class="btn btn-primary btn-sm"
                                    @click.prevent="
                                        updateAlternativa(opcion, index)
                                    "
                                >
                                    <feather-icon icon="EditIcon" />
                                </button>
                            </div>
                        </td>
                        <td class="text-center">
                            <input
                                v-if="!save_mode"
                                type="checkbox"
                                :checked="opcion.correcta"
                                @change="handleCheck(index)"
                            />
                            <input
                                v-else
                                type="checkbox"
                                :checked="opcion.correcta == 'S' ? true : false"
                                @change="(e) => checkAlternativa(e, opcion.id)"
                            />
                        </td>
                        <td class="text-center">
                            <button
                                v-if="!save_mode"
                                class="btn btn-danger btn-sm"
                                @click.prevent="removeOption(index)"
                            >
                                <feather-icon icon="TrashIcon" />
                            </button>
                            <button
                                v-else
                                class="btn btn-danger btn-sm"
                                @click.prevent="deleteAlternativa(opcion.id)"
                            >
                                <feather-icon icon="TrashIcon" />
                            </button>
                        </td>
                    </tr>
                    <!-- Última fila siempre visible para agregar nuevas opciones -->
                    <tr>
                        <td>
                            <textarea
                                class="form-control"
                                v-model="newOption.descripcion"
                                placeholder="Nueva opción"
                                rows="1"
                                style="resize: none; overflow: hidden"
                                @input="autoExpand($event)"
                            ></textarea>
                        </td>
                        <td class="text-center">
                            <!-- Checkbox para la nueva opción -->
                            <input
                                type="checkbox"
                                v-model="newOption.correcta"
                                @change="handleNewOptionCheck"
                            />
                        </td>
                        <td class="text-center">
                            <button
                                v-if="!save_mode"
                                class="btn btn-success btn-sm"
                                @click.prevent="addOption"
                            >
                                <feather-icon icon="SaveIcon" />
                            </button>
                            <button
                                v-if="save_mode"
                                class="btn btn-success btn-sm"
                                @click.prevent="createAlternativa"
                            >
                                <feather-icon icon="SaveIcon" />
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div v-if="save_mode">
            <p v-if="validar_cantidad_alternativas" class="text-danger mt-1">
                Las preguntas de opción simple deben tener al menos dos
                alternativas
            </p>
            <p v-else-if="validar_correctas" class="text-danger mt-1">
                Las preguntas de opción simple deben tener exactamente una
                alternativa correcta.
            </p>
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
    methods: {
        addOption() {
            if (this.newOption.descripcion.trim() !== "") {
                if (this.newOption.correcta) {
                    // Desmarcar todas las demás opciones
                    this.opciones.forEach((opcion) => {
                        opcion.correcta = false;
                        opcion.porcentaje = "0.00";
                    });
                    // Asignar 100% a la nueva opción
                    this.newOption.porcentaje = "100.00";
                }
                this.opciones.push({ ...this.newOption });
                this.newOption = {
                    descripcion: "",
                    porcentaje: "0.00",
                    correcta: false,
                };
            }
        },
        removeOption(index) {
            this.opciones.splice(index, 1);
        },
        autoExpand(event) {
            const element = event.target;
            element.style.height = "auto"; // Restablece la altura
            element.style.height = `${element.scrollHeight}px`; // Ajusta a contenido
        },
        handleCheck(index) {
            const currentOption = this.opciones[index];

            // Si la opción no estaba marcada, marcamos esta opción y le damos 100%
            if (!currentOption.correcta) {
                // Primero desmarcamos todas las demás opciones
                this.opciones.forEach((opcion) => {
                    opcion.correcta = false;
                    opcion.porcentaje = "0.00";
                });

                // Marcamos esta opción como correcta y le damos el porcentaje
                this.$set(currentOption, "correcta", true);
                this.$set(currentOption, "porcentaje", "100.00");
            } else {
                // Si la opción ya estaba marcada, la desmarcamos y ponemos el porcentaje en 0
                this.$set(currentOption, "correcta", false);
                this.$set(currentOption, "porcentaje", "0.00");
            }

            // Emitir el evento después de actualizar el estado
            this.$emit("option-updated", this.opciones);
        },
        handleNewOptionCheck() {
            if (this.newOption.correcta) {
                // Desmarcar todas las demás opciones
                this.opciones.forEach((opcion) => {
                    opcion.correcta = false;
                    opcion.porcentaje = "0.00";
                });
                // Asignar 100% a la nueva opción
                this.newOption.porcentaje = "100.00";
            } else {
                this.newOption.porcentaje = "0.00";
            }
        },
        updateAlternativa(opcion, index) {
            console.log("llega");
            const data = {
                ...opcion,
                index: index + 1,
            };

            this.$emit("update-alternativa", data);
        },
        deleteAlternativa(id) {
            this.$emit("delete", id);
        },
        createAlternativa() {
            const data = {
                descripcion: this.newOption.descripcion,
                porcentaje: this.newOption.porcentaje,
                correcta: this.newOption.correcta ? "S" : "N",
            };

            this.$emit("create", data);

            // Resetear la nueva opción
            this.newOption = {
                descripcion: "",
                porcentaje: "0.00",
                correcta: false,
            };
        },
        checkAlternativa(e, id) {
            const value = e.target.checked;

            const data = {
                id,
                value: value ? "S" : "N",
            };

            this.$emit("update-check", data);
        },
    },
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
</style>
