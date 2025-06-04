<template>
    <div class="table-responsive">
        <table style="overflow: auto" class="table">
            <tr>
                <th style="width: 30%">Alumno</th>
                <th class="aling-center">Total</th>
                <th class="aling-center">Dictadas</th>
                <!-- <th class="aling-center">Dictadas</th> -->
                <th class="aling-center">Asistidas</th>
                <th class="aling-center">Faltas</th>
                <th class="aling-center">%Faltas</th>
                <th class="aling-center">Detalle</th>
            </tr>
            <tbody v-for="(item, index) in asistencias" :key="item.id">
                <tr>
                    <td>{{ item.estudiante }}</td>
                    <td class="aling-center">{{ item.total_clases }}</td>
                    <td class="aling-center">{{ item.total_dictadas }}</td>
                    <td class="aling-center">{{ item.presente }}</td>
                    <td class="aling-center">{{ item.falta }}</td>
                    <td class="aling-center">{{ item.porcentaje_faltas }}%</td>
                    <td class="aling-center">
                        <Button
                            ref="btnEdit"
                            class="btn btn-success btn-sm"
                            @click.native.prevent="
                                detalles(item.id, item.id_matricula, index)
                            "
                        >
                            <feather-icon icon="EyeIcon"></feather-icon>
                        </Button>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class="not-length" v-if="asistencias.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">Aún no se registraron asistencias</span>
        </div>
        <Modal ref="modalDetalle" modal="modal-lg">
            <template #title> Detalle de Asistencia </template>
            <TableDetalleAsistenciaEstudiante
                :periodo="periodo"
                :detalle="detalleView"
                @update-tipo-asistencia="updateTipoAsistencia"
            />
        </Modal>
    </div>
</template>
<script>
import Button from "../../../Components/Button.vue";
import Modal from "../../../Components/Modal.vue";
import TableDetalleAsistenciaEstudiante from "./TableDetalleAsistenciaEstudiante.vue";
export default {
    name: "TableAsistencias",
    components: {
        Button,
        Modal,
        TableDetalleAsistenciaEstudiante,
    },
    props: {
        asistencias: Array,
        periodo: Array,
    },
    data() {
        return {
            detalle: [],
            detalleView: [],
            tipoAsistencia: "",
            idCursoDocente: "",
            idCursoMatricula: "",
        };
    },
    methods: {
        updateTipoAsistencia(data) {
            console.log(this.detalle);
            if (data != "TODOS") {
                this.detalleView = this.detalle.filter(
                    (item) => item.tipo_asistencia == data
                );
            } else {
                this.detalleView = [...this.detalle];
            }
        },
        detalles(id_curso_docente, id_curso_matricula, index) {
            this.idCursoDocente = id_curso_docente;
            this.idCursoMatricula = id_curso_matricula;
            this.$refs.btnEdit[index].load();
            this.$http
                .get(
                    this.routeTo(
                        `asistencia/detalle/${id_curso_docente}/${id_curso_matricula}`
                    )
                )
                .then((res) => {
                    console.log(res.data);
                    this.detalle = res.data;
                    this.detalleView = [...this.detalle];
                    this.$refs.modalDetalle.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit[index].reset();
                });
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

.aling-center {
    text-align: center;
}
.table-evaluacion {
    display: flex;
    justify-content: center;
    align-items: center;
    overflow: auto;
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
        height: 6vh;
        padding-left: 10px;
        min-width: 5vw;
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
