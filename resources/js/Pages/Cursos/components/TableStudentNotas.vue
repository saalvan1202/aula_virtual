<template>
    <div>
        <div class="row">
            <div class="col-md-12 col-lg-12 mb-1" v-if="BDisable">
                <Button
                    ref="btnEdit"
                    class="btn btn-success btn-sm"
                    @click.native.prevent="addNotasManual"
                >
                    <feather-icon icon="Edit2Icon" class="mr-50 ml-20" />
                    <span class="align-small">Ingresar Notas</span>
                </Button>
            </div>
            <div class="col-md-12 col-lg-12 mb-1" v-else>
                <Button
                    ref="btnEdit"
                    class="btn btn-primary btn-sm"
                    @click.native.prevent="addAulaVirtual"
                >
                    <feather-icon icon="Edit2Icon" class="mr-50 ml-20" />
                    <span class="align-small">Subir del Aula Virtual</span>
                </Button>
            </div>
        </div>
        <div class="table-content">
            <table>
                <tr>
                    <!-- <th class="checkBox">
                    <input
                        type="checkbox"
                        @change="selectAllRows"
                        v-model="allRows"
                    />
                </th> -->
                    <th style="text-align: center">ALUMNO</th>
                    <th class="action" style="text-align: center">NOTA</th>
                    <th style="text-align: center" v-if="!BDisable">
                        NO ASISTIÓ
                    </th>
                    <th style="text-align: center" v-else>DETALLES</th>
                </tr>
                <tr v-for="(item, index) in alumnos" :key="index">
                    <td class="data">{{ item.estudiante }}</td>

                    <td
                        class="action"
                        style="display: flex; flex-direction: column"
                    >
                        <BFormInput
                            style="width: 60%; min-width: 65px"
                            @change="
                                changeNotas(item.id, item.nota), sendToParent()
                            "
                            type="number"
                            v-model="item.nota_criterio"
                            min="0"
                            max="20"
                            :value="item.nota"
                            :disabled="BDisable"
                            :class="CInpunt"
                            @input="notaState(index, $event)"
                        />
                    </td>
                    <td
                        style="text-align: center"
                        class="data"
                        v-if="!BDisable"
                    >
                        <input
                            type="checkbox"
                            v-model="item.asistio"
                            @change="notAsistValidation(item)"
                            true-value="N"
                            false-value="S"
                        />
                    </td>
                    <td style="text-align: center" class="data" v-else>
                        <Button
                            v-b-tooltip.hover
                            title="Ver Detalles"
                            ref="btnDetalle"
                            class="btn btn-success btn-sm"
                            @click.native.prevent="
                                getDetalleActividades(
                                    item.id_curso_matricula,
                                    index
                                )
                            "
                        >
                            <feather-icon icon="EyeIcon" class="mr-20" />
                        </Button>
                    </td>
                </tr>
            </table>
        </div>
        <Modal ref="modal">
            <template #title>Detalle de Actividades</template>
            <TableActividades :actividadesC="detalle" :thProp="true" />
        </Modal>
    </div>
</template>

<script>
import {
    BButton,
    BButtonGroup,
    BFormGroup,
    BTable,
    BFormInput,
    BFormInvalidFeedback,
} from "bootstrap-vue";
import { alertSuccess, alertWarning } from "../../../sweetAlert2";
import Button from "../../../Components/Button.vue";
import { useForm } from "@inertiajs/vue2";
import Modal from "../../../Components/Modal.vue";
import TableActividades from "./TableActividades.vue";

export default {
    name: "TableStudentNotas",
    components: {
        BFormGroup,
        BButton,
        BTable,
        BFormInput,
        BFormInvalidFeedback,
        Button,
        Modal,
        TableActividades,
    },
    props: {
        estudiantes: Object,
        items: Array,
        alumnos: Array,
        CInpunt: String,
        actividades: Array,
        uuid: String,
        id_criterio_indicador: String,
    },
    data() {
        return {
            detalle: [],
            modes: ["multi", "single", "range"],
            alumnosView: [],
            fields: [
                "selected",
                "curso",
                "seccion",
                "creditos de Teoria",
                "Creditos de Práctica",
            ],
            notas: [],
            form: useForm({
                id_curso_matricula: "",
                actividades: [],
            }),
            selectMode: "multi",
            selected: [],
            allRows: "",
            note: null,
            BDisable: true,
        };
    },
    mounted() {
        console.log(this.alumnos);
    },

    methods: {
        notAsistValidation(data) {
            console.log(data);
            if (data.asistio == "N") {
                data.nota_criterio = 0;
            }
        },
        getDetalleActividades(id, index) {
            this.form.id_curso_matricula = id;
            this.form.actividades = this.actividades;
            this.$refs.btnDetalle[index].load();
            this.$http({
                method: "POST",
                url: this.routeTo(`calificaciones/detalle-notas`),
                data: this.form.data(),
            })
                .then((res) => {
                    this.detalle = res.data;
                    this.$refs.modal.show();
                })
                .catch((error) => {})
                .finally(() => {
                    this.$refs.btnDetalle[index].reset();
                    this.form.processing = false;
                });
        },
        addAulaVirtual() {
            this.BDisable = true;
            this.$emit("agregar-notas-manual", false);
        },
        addNotasManual() {
            console.log(this.actividades);
            this.$emit("agregar-notas-manual", true);
            this.BDisable = false;
        },
        notaState(index, event) {
            const min = 0;
            const max = 20;
            console.log(event);
            let inputValue = parseInt(event, 10);
            if (inputValue > max) {
                inputValue = max;
                console.log(inputValue, "Mayor que 20");
                this.alumnos[index].nota = inputValue;
            }
            if (inputValue < min) {
                inputValue = min;
                console.log(inputValue);
                this.alumnos[index].nota = inputValue;
            }
        },
        changeNotas(id, nota) {
            console.log(this.alumnos);
            this.note = nota;
            const index = this.notas.findIndex((item) => item.id === id);

            if (index !== -1) {
                // Si existe, actualiza el objeto con el nuevo valor de nota
                this.notas[index].nota = nota;
            } else {
                // Si no existe, agrega un nuevo objeto con id y nota
                this.notas.push({ id, nota });
            }
            console.log(this.notas);
        },
        sendToParent() {
            // Emite un evento llamado 'sendMessage' con la variable 'message'
            this.$emit("sendNotes", this.notas);
        },
        sendToGDisable() {
            this.$emit("GDisable", this.GDisable);
        },
        onCursos() {
            console.log(this.estudiantes);
            if (
                this.estudiantes.id_periodo_clases &&
                this.estudiantes.estudiante
            ) {
                this.onPostCursos();
            } else {
                alertWarning(
                    "Seleccione todos los filtros para poder consultar los cursos"
                );
                return;
            }
        },
        onRowSelected(id) {
            console.log(this.items);
            console.log(id);
            if (this.selected.includes(id)) {
                // Si el id ya está en el array, lo eliminamos (deseleccionar)
                this.selected = this.selected.filter((item) => item !== id);
                this.allRows = false;
            } else {
                // Si no está en el array, lo agregamos
                this.selected.push(id);
            }
            this.sendToParent();
        },
        selectAllRows() {
            this.items = this.items.map((item) => {
                return { ...item, check: this.allRows };
            });
            this.validateRowSelect();
            this.sendToParent();
        },
        validateRowSelect() {
            this.selected = this.items
                .filter((item) => item.check === true)
                .map((item) => item.id);
        },
        addCheckArray() {
            this.items = this.items.map((item) => {
                return { ...item, check: "" };
            });
            console.log(this.items);
        },
        clearSelected() {
            this.$refs.selectableTable.clearSelected();
        },
        selectThirdRow() {
            // Rows are indexed from 0, so the third row is index 2
            this.$refs.selectableTable.selectRow(2);
        },
        unselectThirdRow() {
            // Rows are indexed from 0, so the third row is index 2
            this.$refs.selectableTable.unselectRow(2);
        },
    },
};
</script>
<style scoped>
.modal-body {
    padding-top: 0.1rem !important;
}
.table-content {
    overflow: auto;
}
table {
    .action {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh;
        padding-left: 0px;
    }

    width: 100%;
    th {
        padding-left: 10px;
        min-width: 8vw;
        background-color: #f3f2f7;
    }
    td {
        padding: 30px 0 10px 10px;
    }
    .inputD {
        height: 6vh;
        width: 8vh;
        text-align: center;
    }
    .data {
        padding: 15px 0 10px 10px;
    }
    .inputE {
        height: 6vh;
        width: 8vh;
        text-align: center;
        outline: none;
    }
}
</style>
