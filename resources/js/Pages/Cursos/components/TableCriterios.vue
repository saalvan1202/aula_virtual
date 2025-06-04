<template>
    <div>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Evaluación</th>
                    <th>Semana</th>
                    <th>Peso</th>
                    <th>Estado</th>
                    <th v-if="estadoCurso != 'S'">Gestión</th>
                </tr>
                <tbody>
                    <tr class="indicador">
                        <td colspan="5" style="background-color: #c9c9c9">
                            Criterios de Evalución
                        </td>
                    </tr>
                    <tr v-for="item in evaluacionesView">
                        <td>{{ item.nombre }}</td>
                        <td>{{ searchSemana(semanas, item.semana) }}</td>
                        <td>{{ parseInt(item.porcentaje) }}%</td>
                        <td>
                            <!-- ESTADO DE CALIFICACIÓN -->
                            <BBadge
                                variant="light-danger"
                                v-if="item.estado_calificacion == 'PENDIENTE'"
                            >
                                {{ item.estado_calificacion }}
                            </BBadge>
                            <BBadge
                                variant="light-success"
                                v-else-if="
                                    item.estado_calificacion == 'CALIFICADO'
                                "
                            >
                                {{ item.estado_calificacion }}
                            </BBadge>
                            <BBadge variant="light-primary" v-else>
                                {{ item.estado_calificacion }}
                            </BBadge>
                        </td>
                        <td v-if="estadoCurso != 'S'">
                            <Button
                                title="Calificar Criterio"
                                ref="btnSucces"
                                class="btn btn-primary btn-sm"
                                @click="getCalificar(item.id)"
                            >
                                <feather-icon icon="PlusIcon" class="mr-20" />
                                <span class="align-small">Calificar</span>
                            </Button>
                            ||
                            <Button
                                title="Editar Criterio"
                                ref="btnEdit"
                                class="btn btn-warning btn-sm btn-icon"
                                @click="edit(item.id)"
                            >
                                <feather-icon icon="EditIcon" class="mr-10" />
                            </Button>
                            <Button
                                title="Eliminar Criterio"
                                ref="btnDestroy"
                                class="btn btn-danger btn-sm btn-icon"
                                @click="destroy(item.id)"
                            >
                                <feather-icon icon="Trash2Icon" class="mr-20" />
                            </Button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="not-length" v-if="evaluacionesView.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small"
                >No se registraron criterios de evaluacion</span
            >
        </div>
    </div>
</template>
<script>
import { alertSuccess, confirm } from "../../../sweetAlert2";
import { searchSemana } from "../../../helpers";
import { BBadge } from "bootstrap-vue";
export default {
    name: "TableCriterios",
    props: {
        evaluaciones: Array,
        urlRouteCriterios: String,
        semanas: Array,
        uuid: String,
        estadoCurso: String,
    },
    data() {
        return {
            evaluacionesView: [],
        };
    },
    mounted() {
        if (!this.evaluaciones) {
            return;
        } else {
            this.evaluacionesView = this.evaluaciones;
        }
    },
    methods: {
        searchSemana,
        getCalificar(id) {
            this.$inertia.visit(
                this.routeTo(
                    `gestion-cursos/gestion_evaluaciones/${this.uuid}/${id}`
                )
            );
        },
        deleteEvaluacion(id) {
            const index = this.evaluacionesView.findIndex(
                (item) => item.id === id
            );
            this.evaluacionesView.splice(index, 1);
            console.log(this.evaluacionesView);
        },
        destroy(id) {
            confirm(
                {
                    text: "Desea eliminar el criterio de evaluación seleccionado?",
                },
                () => {
                    this.$http
                        .delete(this.routeTo(`${this.urlRouteCriterios}${id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.deleteEvaluacion(id);
                        })
                        .catch((error) => {})
                        .finally(() => {});
                }
            );
        },
        edit(id) {
            this.$emit("editE", id);
        },
    },
    computed: {},
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
@keyframes scaleIn {
    0% {
        opacity: 0; /* Comienza invisible */
        height: 10%;
    }
    30% {
        height: 30%;
    }
    60% {
        height: 60%;
    }
    70% {
        height: 70%;
    }
    90% {
        height: 90%;
    }
    100% {
        opacity: 1;
        height: 100%;
    }
}
.table-evaluacion {
    display: flex;
    justify-content: center;
    align-items: center;
    animation: scaleIn 1s ease forwards;
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
        background-color: #f3f2f7;
    }
    td {
        background-color: white;
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
