<template>
    <div>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="row">
                    <div class="col-md-4 col-lg-4">
                        <div class="form-group">
                            <label>Estado</label>
                            <select
                                class="form-control"
                                v-model="tipo_asistencia"
                                @change="updateTipoAsistencia"
                            >
                                <option value="TODOS">TODOS</option>
                                <option value="PRESENTE">ASISTENCIAS</option>
                                <option value="FALTA">INASISTENCIAS</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-evaluacion">
            <table>
                <tr>
                    <th>Clase</th>
                    <th>Fecha</th>
                    <th>Día</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th>Estado</th>
                </tr>
                <tbody v-for="item in detalle">
                    <tr>
                        <td>{{ item.numero }}</td>
                        <td>{{ convertDate(item.fecha, "-", "/") }}</td>
                        <td>{{ item.dia }}</td>
                        <td>{{ item.hora_inicio }}</td>
                        <td>{{ item.hora_fin }}</td>
                        <td>
                            <BBadge
                                variant="light-success"
                                v-if="item.tipo_asistencia == 'PRESENTE'"
                            >
                                <feather-icon icon="UserIcon"></feather-icon>
                                <span> Asistió </span>
                            </BBadge>
                            <BBadge
                                variant="light-danger"
                                v-else-if="item.tipo_asistencia == 'FALTA'"
                            >
                                <feather-icon icon="XIcon"></feather-icon>
                                <span> Faltó </span>
                            </BBadge>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="not-length" v-if="detalle.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">No se registraron asistencias</span>
        </div>
    </div>
</template>
<script>
import { BBadge } from "bootstrap-vue";
import { convertDate } from "../../../helpers";
export default {
    name: "TableDetalleAsistenciaEstudiante",
    props: {
        evaluaciones: Array,
        periodo: Array,
        detalle: Array,
    },
    components: {
        BBadge,
    },
    data() {
        return {
            tipo_asistencia: "TODOS",
        };
    },
    mounted() {
        this.updateTipoAsistencia();
    },
    methods: {
        updateTipoAsistencia() {
            this.$emit("update-tipo-asistencia", this.tipo_asistencia);
        },
        convertDate,
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
