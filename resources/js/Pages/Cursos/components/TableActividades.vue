<template>
    <div class="table-evaluacion">
        <table>
            <tr>
                <th style="text-align: center">ACTIVIDAD</th>
                <th style="text-align: center">FECHA</th>
                <!-- //SE MANDA EL PROP PARA QUE SE MUESTRE LA CALIFICAIC´PON EN EL DETALLE -->
                <th style="text-align: center" v-if="thProp">ESTADO</th>
                <th style="text-align: center" v-if="thProp">CALIFICACION</th>
                <th style="text-align: center" v-else>DETALLE</th>
            </tr>
            <tbody>
                <tr v-for="item in actividadesC" :key="item.id">
                    <td>{{ item.nombre }}</td>
                    <td style="text-align: center">{{ item.fecha_inicio }}</td>
                    <td
                        style="text-align: center"
                        v-if="item.estado_calificacion"
                    >
                        <p>
                            <BBadge
                                variant="light-success"
                                v-if="item.estado_calificacion == 'CALIFICADO'"
                            >
                                {{ item.estado_calificacion }}
                            </BBadge>
                            <BBadge variant="light-danger" v-else>
                                {{ item.estado_calificacion }}
                            </BBadge>
                        </p>
                    </td>
                    <td style="text-align: center">
                        <p v-if="item.puntaje">
                            <BBadge
                                variant="light-success"
                                v-if="item.puntaje > 10.5"
                            >
                                {{ item.puntaje }}
                            </BBadge>
                            <BBadge variant="light-danger" v-else>
                                {{ item.puntaje }}
                            </BBadge>
                        </p>
                        <Button
                            title="Agregar Actividad"
                            ref="btnAdd"
                            class="btn btn-primary btn-sm"
                            @click="addActividades(item)"
                            v-else
                        >
                            <feather-icon
                                icon="CheckCircleIcon"
                                class="mr-20"
                            />
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="not-length" v-if="actividadesC.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">No se encontraron actividades</span>
        </div>
    </div>
</template>
<script>
import { BCardText, BFormInput, BInputGroup, BTab, BTabs } from "bootstrap-vue";
import Card from "../../../Components/Card.vue";
import Modal from "../../../Components/Modal.vue";
import { confirm } from "../../../sweetAlert2";

export default {
    name: "TableActividades",
    props: {
        evaluaciones: Array,
        actividadesC: Array,
        actividadesAgregadas: Array,
        thProp: Boolean,
    },
    components: {
        BFormInput,
        BInputGroup,
        Card,
        Modal,
        BTab,
        BTabs,
        BCardText,
    },
    data() {
        return {
            pesoNota: "",
            actividades: [],
        };
    },
    methods: {
        close() {
            this.$refs.modalActividades.hide();
        },
        create() {
            this.$refs.modalActividades.show();
        },
        ponderar() {
            this.pesoNota = 100 / this.actividades.length;
        },
        addActividades(data) {
            this.actividades = [...this.actividadesAgregadas];
            confirm(
                {
                    text: "Desea agregar el registro seleccionado?",
                    confirmButtonText: "Agregar",
                    customClass: {
                        confirmButton: "btn btn-success",
                    },
                },
                () => {
                    this.actividades.push(data);
                    this.$emit("update-actividades", this.actividades);
                }
            );
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
.not-length .feather {
    height: 4rem !important;
}
.title-actividades {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.title-actividades .feather {
    height: 2rem;
}
.table-evaluacion {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-top: 30px;
    margin-bottom: 20px;
    justify-content: center;
    align-items: center;
}
table {
    .indicador {
        background-color: #c9c9c9;
        width: 100%;
        font-weight: bold;
    }
    .action {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh;
        padding-left: 0px;
    }

    width: 100%;
    th {
        height: 4vh;
        padding-left: 10px;
        min-width: 8vw;
        background-color: #f3f2f7;
    }
    td {
        padding: 8px 0 10px 10px;
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
    .btn {
    }
}
</style>
