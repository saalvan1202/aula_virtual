<template>
    <div>
        <div
            v-if="estudiantes.length != 0"
            style="display: flex; flex-direction: column"
        >
            <div class="datos mb-2">
                <div>
                    <section class="datos-curso">
                        <p><strong>Unidad Didáctica:</strong></p>
                        <p>{{ curso.curso }}</p>
                    </section>
                </div>
                <div>
                    <section class="datos-curso">
                        <p><strong>Ciclo:</strong></p>
                        {{ curso.ciclo }}
                    </section>
                    <section class="datos-curso">
                        <p><strong>Turno:</strong></p>
                        {{ curso.turno }}
                    </section>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th>Alumno</th>
                        <th
                            style="text-align: center"
                            v-for="(item, index) in estudiantes[0].indicadores"
                            title="Indicadores"
                        >
                            {{ `I${index + 1}` }}
                        </th>
                        <th>Promedio</th>
                    </tr>
                    <tbody v-for="item in estudiantesView">
                        <tr>
                            <td style="text-align: start">
                                {{ item.estudiante }}
                            </td>
                            <td
                                v-for="indicador in item.indicadores"
                                style="text-align: center"
                            >
                                {{ indicador.nota }}
                            </td>
                            <td v-if="item.estado_nota != 'INHABILITADO'">
                                {{ item.promedio }}
                            </td>
                            <td v-else-if="item.estado_nota == 'INHABILITADO'">
                                INH
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div
                    style="
                        display: flex;
                        justify-content: end;
                        padding-left: 5px;
                        padding-right: 5px;
                    "
                    class="mb-1 mt-2"
                >
                    <button
                        class="btn btn-success btn-sm"
                        @click="publicarNotasFinales"
                    >
                        <feather-icon icon="UploadIcon" />
                        <span>Publicar Nota Final</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="not-length" v-if="estudiantes.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">No se registraron notas</span>
        </div>
    </div>
</template>
<script>
import { BBadge } from "bootstrap-vue";
import { alertSuccess, alertWarning, confirm } from "../../../sweetAlert2";

export default {
    components: {
        BBadge,
    },
    name: "TableCalificacionesDocentes",
    props: {
        evaluaciones: Array,
        estudiantes: Array,
        curso: Object,
    },
    data() {
        return { bg_badge: "light-success", estudiantesView: [] };
    },
    methods: {
        publicarNotasFinales() {
            this.$http({
                method: "POST",
                url: this.routeTo(`promedios/subir-notas`),
                data: this.estudiantesView,
            })
                .then((res) => {
                    alertSuccess("Notas publicadas Correctamente");
                })
                .catch((error) => {})
                .finally(() => {});
        },
        inhabilitar(item) {
            confirm(
                {
                    text: "Estás seguro de inhabilitar a este estudiante?",
                    confirmButtonText: "Inhabilitar",
                    customClass: {
                        confirmButton: "btn btn-danger",
                    },
                },
                () => {
                    this.$http({
                        method: "POST",
                        url: this.routeTo(
                            `promedios/inhabilitar/${item.id_curso_matricula}`
                        ),
                    })
                        .then((res) => {
                            alertSuccess("Alumno Inhabilitado Correctamente");
                            item.promedio = "INH";
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.form.processing = false;
                            this.getIndicadores();
                        });
                }
            );
        },
    },
    watch: {
        estudiantes: {
            immediate: true, // Ejecuta el watcher inmediatamente al montar
            handler(newValue) {
                this.estudiantesView = [...newValue]; // Actualiza el estado local con los nuevos datos
            },
        },
    },
};
</script>
<style scoped>
.datos {
    display: flex;
    gap: 30px;
}
.datos-curso {
    display: flex;
    height: 3vh;
    gap: 5px;
    margin-bottom: 5px;
}
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
    flex-direction: column;
    justify-content: center;
    align-items: center;
}
table {
    width: 100%;
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

    th {
        height: 6vh;
        padding-left: 10px;
        min-width: 4.5vw;
        background-color: #f3f2f7;
    }
    td {
        text-align: center;
        border-bottom: 1px solid #f3f2f7;
        border-right: 1px solid #f3f2f7;
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
}
@media (max-width: 430px) {
    .datos {
        flex-direction: column;
    }
}
</style>
