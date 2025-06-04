<template>
    <div class="table-responsive">
        <table>
            <tr>
                <th class="checkBox">
                    <input
                        type="checkbox"
                        @change="selectAllRows"
                        v-model="allRows"
                    />
                </th>
                <th>CURSO</th>
                <th>SECCION</th>
                <th>CREDITOS</th>
            </tr>
            <tr v-for="(item, index) in items" :key="index">
                <td class="checkBox">
                    <input
                        type="checkbox"
                        v-model="item.check"
                        @change="onRowSelected(item.id)"
                    />
                </td>
                <td>{{ item.curso }}</td>
                <td>{{ item.seccion }}</td>
                <td>{{ item.creditos_teoria + item.creditos_practica }}</td>
            </tr>
        </table>
        <div class="not-length" v-if="items.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">No se enontraton cursos</span>
        </div>
    </div>
</template>

<script>
import { BButton, BButtonGroup, BFormGroup, BTable } from "bootstrap-vue";
import { alertWarning } from "../sweetAlert2";

export default {
    name: "SelectTable",
    components: {
        BFormGroup,
        BButton,
        BTable,
    },
    props: {
        estudiantes: Object,
        items: Array,
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
            selectMode: "multi",
            selected: [],
            allRows: "",
        };
    },

    methods: {
        sendToParent() {
            // Emite un evento llamado 'sendMessage' con la variable 'message'
            this.$emit("sendMessage", this.selected);
        },
        onRowSelected(id) {
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
.not-length {
    height: 50px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
table {
    .checkBox {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh;
        padding-left: 0px;
    }
    width: 100%;
    th {
        padding-left: 10px;
        height: 100%;
        min-height: 5vh;
        min-width: 8vw;
        background-color: #f3f2f7;
    }
    td {
        padding: 10px 0 10px 20px;
    }
}
</style>
