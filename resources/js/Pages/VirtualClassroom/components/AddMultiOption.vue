<template>
    <div style="width: 100%; max-height: 400px; overflow-y: scroll">
        <table class="table table-bordered" style="width: 100%">
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
                <tr v-for="(opcion, index) in opciones" :key="index">
                    <td>
                        <textarea
                            v-if="!save_mode"
                            class="form-control"
                            v-model="opcion.descripcion"
                            placeholder="Alternativa"
                            rows="1"
                            style="resize: none; overflow: hidden; width: 100%"
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

                <tr>
                    <td>
                        <textarea
                            class="form-control"
                            v-model="newOption.descripcion"
                            placeholder="Nueva opción"
                            rows="1"
                            style="resize: none; overflow: hidden; width: 100%"
                            @input="autoExpand($event)"
                        ></textarea>
                    </td>
                    <td class="text-center">
                        <input type="checkbox" v-model="newOption.correcta" />
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
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { required, numeric, minValue, maxValue } from "@vuelidate/validators";
import Modal from "../../../Components/Modal.vue";
import InputSchedule from "../../Schedule/components/InputSchedule.vue";
import { useForm } from "@inertiajs/vue2";
import { alertWarning } from "../../../sweetAlert2";

export default {
    name: "AddMultiOption",
    components: { Modal, InputSchedule },
    props: {
        value: Array, // v-model para enlazar el array de opciones con el padre
        save_mode: {
            type: Boolean,
            default: false,
        },
    },
    data() {
        return {
            newOption: useForm({
                descripcion: "",
                porcentaje: "0.00",
                correcta: false,
            }),
            formPuntaje: useForm({
                id: null,
                descripcion: "",
                porcentaje: "0.00",
                index: null,
            }),
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
        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        resetInput(form, field) {
            form[field] = "";
        },
        addOption() {
            if (this.newOption.descripcion.trim() !== "") {
                const newId = this.opciones.length + 1; // Generar un ID único
                this.opciones.push({
                    id: newId, // Asignar un ID único
                    ...this.newOption,
                });
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
            console.log("funcionando");
            const currentOption = this.opciones[index];
            // Cambiar el estado de correcta
            this.$set(currentOption, "correcta", !currentOption.correcta);

            // Si se desmarcó la opción, establecer su porcentaje en 0
            if (!currentOption.correcta) {
                this.$set(currentOption, "porcentaje", "0.00");
            }
        },
        // Método para formatear el porcentaje a 2 decimales
        formatPercentage(value) {
            const new_value = parseFloat(value).toFixed(2);
            return new_value; // Formatear a 2 decimales
        },
        // Método para actualizar el porcentaje de una opción existente
        updatePercentage(event, index) {
            const newPercentage = parseFloat(event.target.value);
            if (!isNaN(newPercentage)) {
                this.$set(this.opciones, index, {
                    ...this.opciones[index],
                    porcentaje: newPercentage,
                });
            }
        },
        // Método para actualizar el porcentaje de la nueva opción
        updateNewOptionPercentage(event) {
            const newPercentage = parseFloat(event.target.value);
            if (!isNaN(newPercentage)) {
                this.newOption.porcentaje = newPercentage;
            }
        },

        updateAlternativa(opcion, index) {
            const data = {
                ...opcion,
                index: index + 1,
            };

            this.$emit("update-alternativa", data);
        },
        deleteAlternativa(id) {
            this.$emit("delete", id);
        },
        checkAlternativa(e, id) {
            const value = e.target.checked;

            const data = {
                id,
                value: value ? "S" : "N",
            };

            this.$emit("update-check", data);
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
    },
    setup() {
        return {
            v$: useVuelidate(),
        };
    },
    validations() {
        return {
            formPuntaje: {
                porcentaje: {
                    required,
                    numeric,
                    minValue: minValue(0),
                    maxValue: maxValue(100),
                },
            },
        };
    },
};
</script>

<style scoped>
.button-percent {
    gap: 4px;
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

textarea {
    font-size: 13px;
}

.font-percent {
    font-size: 11px;
}

.calcular-puntajes-container {
    display: flex;
    gap: 6px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 8px;

    button {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }

    .calcular-btn {
        display: flex;
        align-items: center;
        cursor: pointer;

        .title {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #82868b;
            color: white;
            height: 100%;
            min-width: 80px;
            padding: 0 10px;
            border-left: 1px solid #ffffff;

            border-top-right-radius: 0.358rem;
            border-bottom-right-radius: 0.358rem;

            p {
                font-size: 12px;
            }
        }

        .title-dark {
            background-color: #4b4b4b;
        }
    }
}

.edit-text {
    display: flex;
}
</style>
