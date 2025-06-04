<template>
    <div>
        <table>
            <tr>
                <!-- <th class="checkBox">
                    <input
                        type="checkbox"
                        @change="selectAllRows"
                        v-model="allRows"
                    />
                </th> -->
                <th>ALUMNO</th>
                <th>NO RINDIÓ</th>
                <th class="action">NOTA</th>
            </tr>
            <tr v-for="(item, index) in alumnos" :key="index">
                <!-- <td class="checkBox">
                    <input
                        type="checkbox"
                        v-model="item.check"
                        @change="onRowSelected(item.id)"
                    />
                </td> -->
                <td>{{ item.estudiante }}</td>
                <td><input type="checkbox" /></td>
                <td
                    class="action"
                    style="display: flex; flex-direction: column"
                >
                    <BFormInput
                        @change="
                            changeNotas(item.id, item.nota), sendToParent()
                        "
                        type="number"
                        v-model="item.nota"
                        min="0"
                        max="20"
                        :value="item.nota"
                        :disabled="BDisable"
                        :class="CInpunt"
                        @input="notaState(index, $event)"
                    />
                </td>
            </tr>
        </table>
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
import { alertWarning } from "../sweetAlert2";

export default {
    name: "SelectTable",
    components: {
        BFormGroup,
        BButton,
        BTable,
        BFormInput,
        BFormInvalidFeedback,
    },
    props: {
        estudiantes: Object,
        items: Array,
        alumnos: Array,
        BDisable: Boolean,
        CInpunt: String,
    },
    data() {
        return {
            modes: ["multi", "single", "range"],
            fields: [
                "selected",
                "curso",
                "seccion",
                "creditos de Teoria",
                "Creditos de Práctica",
            ],
            notas: [],
            selectMode: "multi",
            selected: [],
            allRows: "",
            note: null,
            GDisable: true,
        };
    },
    computed: {},
    methods: {
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
        onPostCursos() {
            this.$http({
                method: "POST",
                url: this.routeTo(`matriculas/searchCursos`),
                data: this.estudiantes,
            })
                .then((res) => {
                    console.log(res.data);
                    if (!res.data.estadoMatricula) {
                        this.items = res.data;
                    } else {
                        alertWarning(
                            "El estudiante ya tiene una matricula registrada en este periodo de clases"
                        );
                        return;
                    }
                    this.addCheckArray();
                    alertSuccess("Datos Guardados Correctamente");
                })
                .catch((error) => {})
                .finally(() => {});
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
        padding: 30px 0 10px 30px;
    }
    .inputD {
        height: 6vh;
        width: 10vh;
        text-align: center;
    }
    .inputE {
        height: 6vh;
        width: 10vh;
        text-align: center;
        outline: none;
    }
}
</style>
