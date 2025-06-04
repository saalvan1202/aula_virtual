<template>
    <LayoutContent>
        <section class="estudiantes-cursos">
            <div class="row mb-1">
                <div class="col-md-12 col-lg-12 mb-2">
                    <Button
                        ref="btnEdit"
                        class="btn btn-secondary"
                        @click.native.prevent="remember"
                    >
                        <feather-icon icon="ArrowLeftIcon" class="mr-50" />
                        <span class="align-small">Volver</span>
                    </Button>
                </div>
                <div class="col-md-12 col-lg-12">
                    <h4 class="card-title">Gestion de Calificaciones</h4>
                </div>
                <div class="col-md-6 col-lg-6">
                    <TableConfiguracionNotas
                        :tareas="tareas"
                        :foros="foros"
                        :criterio="criterio"
                        :uuid="uuid"
                        @set-pre-notas="setPreNotas"
                        :globalStateV="globalStateV"
                        :storeF="store"
                        @notas-aula="notasAula"
                        @actividades="getActividades"
                    />
                </div>
                <div class="col-md-6 col-lg-6">
                    <Card>
                        <template #header>
                            <h4 class="card-title">Visualización</h4>
                        </template>
                        <TableStudentNotas
                            :alumnos="alumnosView"
                            :uuid="uuid"
                            :id_criterio_indicador="id_criterio_indicador"
                            @agregar-notas-manual="agregarNotasManual"
                            :actividades="actividades"
                        />
                    </Card>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <!--ESPACIO PARA EL SPIN  -->
                    <div class="spinner" v-if="loadingCursos">
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>
                </div>
            </div>
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import CardCourse from "../../Components/CardCourse.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import { alertSuccess } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
import { BSpinner } from "bootstrap-vue";
import TableStudent from "../../Components/TableStudent.vue";
import CardCursoInfo from "./components/CardCursoInfo.vue";
import TableConfiguracionNotas from "./components/TableConfiguracionNotas.vue";
import TableStudentNotas from "./components/TableStudentNotas.vue";
export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        BSpinner,
        CardCourse,
        TableStudent,
        CardCursoInfo,
        TableConfiguracionNotas,
        TableStudentNotas,
    },
    name: "CalificaciónCriterios",
    props: {
        urlRoute: String,
        alumnos: Array,
        tareas: Array,
        foros: Array,
        criterio: Object,
        uuid: String,
        id_criterio_indicador: String,
    },
    data() {
        return {
            path: "",
            loadingCursos: false,
            cursos: [],
            actividades: [],
            globalStateV: false,
            alumnosView: [],
            id_periodo: "",
            id_seccion: "",
            id_ciclo: "",
            id_turno: "",
            columns: [
                {
                    data: null,
                    title: "#",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                },
                { data: "instituto", title: "Instituto" },
                { data: "descripcion", title: "Sede" },
            ],
            form: useForm({
                id_criterio_evaluacion_indicador: 0,
                alumnos: [],
            }),
            parents: [],
        };
    },

    mounted() {
        //ASIGNAMOS EL VALOR DEL ID_CRITERIO, PARA SUBIR LAS NOTAS
        this.form.id_criterio_evaluacion_indicador = this.criterio.id;
        this.form.alumnos = this.alumnos;
        this.alumnosView = this.alumnos;
    },
    methods: {
        getEstudiantes() {
            this.$http
                .get(
                    this.routeTo(
                        `calificaciones/get-estudiantes?uuid=${this.uuid}&id=${this.id_criterio_indicador}`
                    )
                )
                .then((res) => {
                    this.alumnosView = res.data;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnStore.reset();
                });
        },
        getActividades(data) {
            this.actividades = data;
        },
        notasAula(data) {
            this.globalStateV = data;
        },
        agregarNotasManual(data) {
            this.globalStateV = data;
        },
        setPreNotas(data) {
            this.alumnosView = this.alumnosView.map((alumno) => {
                const match = data.find((item) => {
                    return item.id_matricula == alumno.id_curso_matricula;
                });
                if (match) {
                    return {
                        ...alumno,
                        nota_criterio: match.nota_final,
                    };
                }
                return alumno;
            });
            console.log(data);
            console.log("padre", this.alumnosView);
        },
        remember() {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/indicadores/${this.uuid}`)
            );
        },
        //Estudiantes Matriculados en el Curso
        setEstudiantesMatriculados() {
            let id = parseFloat(this.curso.id);
            let uuid = this.curso.uuid;
            this.$inertia.visit(
                this.routeTo(`${this.urlRoute}/matriculados/${id}/${uuid}`)
            );
        },
        setGestion() {
            this.$inertia.visit(this.routeTo(`gestion-cursos`));
        },
        setCursos(uuid, id, id_periodo_clases) {
            this.$inertia.visit(
                //El Id es el de curso_docente
                this.routeTo()
            );
        },
        setForm(data) {
            this.setFormData(this.form, data);
        },
        create() {
            this.form.reset();
            this.form.id_instituto = firstId(this.institutos);
            this.$refs.modal.show();
        },
        close() {
            this.$refs.modal.hide();
        },
        store() {
            this.form.processing = true;
            this.form.alumnos = this.alumnosView;
            this.$http({
                method: "POST",
                url: this.routeTo(`calificaciones/subir-notas`),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Notas Guardadas Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.getEstudiantes();
                    this.form.processing = false;
                });
        },
        getCursos() {
            this.loadingCursos = true;
            this.$http
                .get(
                    this.routeTo(
                        `${this.urlRoute}/${this.id_periodo}?turno=${this.id_turno}&seccion=${this.id_seccion}&ciclo=${this.id_ciclo}`
                    )
                )
                .then((res) => {
                    console.log(res.data);
                    this.cursos = res.data;
                    this.loadingCursos = false;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                    this.loadingCursos = false;
                });
        },
        edit() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.form.reset();
            this.$refs.btnEdit.load();
            this.$http
                .get(this.routeTo(`${this.urlRoute}/${row.id}/edit`))
                .then((res) => {
                    this.setForm(res.data);
                    this.$refs.modal.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                });
        },
        destroy() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    this.$refs.btnDestroy.load();
                    this.$http
                        .delete(this.routeTo(`${this.urlRoute}/${row.id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$refs.datatable.reload();
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.$refs.btnDestroy.reset();
                        });
                }
            );
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors || {};
        },
    },
};
</script>
<style scoped>
.imgContent {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
    width: 100%;
    .feather {
        color: black;
        height: 10vh;
        margin-bottom: 8px;
    }
}
.spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.card-title {
    display: -webkit-box; /* Usa un contenedor de caja flexible */
    -webkit-line-clamp: 3; /* Limita el texto a 3 líneas */
    -webkit-box-orient: vertical; /* Define la dirección de las líneas (vertical) */
    overflow: hidden; /* Oculta cualquier contenido que se desborde */
    text-overflow: ellipsis;
}
.cursos {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.state {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    width: 4vw;
    background-color: rgb(5, 207, 5);
    font-size: 12px;
    color: white;
}
.state :hover {
    background-color: rgb(5, 207, 5);
}
.card-bodys {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.info-curso {
    display: flex;
    align-items: center;
    p {
        margin-bottom: 0;
    }
    .feather {
        height: 3.5vh;
        margin-bottom: 8px;
    }
}

.informacion {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.filter-title {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: space-between;
}
.datos {
    display: flex;
    gap: 30px;
}
.datos-curso {
    display: flex;
}
.datos-header {
    display: flex;
    width: 100%;
    justify-content: space-between;
    align-items: center;
}
.datos-button {
    display: flex;
    justify-content: end;
    align-items: center;
}
</style>
