<!--
*-Falta manejar las horas en las que se van a dar las clases
*-Que los número de las clases sean únicos
*-Opción para ordenar las clases
-->
<template>
    <LayoutContent>
        <section class="indicadores-cursos">
            <div class="row mb-1">
                <div class="col-md-6 col-lg-6">
                    <Button
                        ref="btnEdit"
                        class="btn btn-secondary"
                        @click.native.prevent="remember"
                    >
                        <feather-icon icon="ArrowLeftIcon" class="mr-50" />
                        <span class="align-small">Volver</span>
                    </Button>
                </div>
                <div
                    class="col-md-6 col-lg-6 mb-2"
                    style="display: flex; justify-content: end"
                >
                    <button
                        title="Agregar nueva clase"
                        class="btn btn-primary"
                        @click.prevent="create"
                        v-if="!culminado"
                    >
                        <feather-icon icon="PlusIcon" class="mr-50" />
                        <span class="align-middle">Nuevo</span>
                    </button>
                </div>
            </div>
            <h4 class="mb-2">Gestión de Clases</h4>
            <div class="not-length" v-if="clasesView.length == 0">
                <feather-icon icon="InboxIcon" />
                <span class="align-small">Sin clases registradas</span>
            </div>
            <div class="row">
                <div
                    class="card-deck col-md-6 col-lg-6 mb-3"
                    v-for="(item, index) in clasesView"
                    :key="item.id"
                >
                    <BCard header-text-variant="black" align="center">
                        <template #header>
                            <h5
                                class="text-black header-indicador"
                                style="display: flex; justify-content: center"
                            >
                                <strong>
                                    <feather-icon
                                        icon="ArchiveIcon"
                                        class="mr-50"
                                    />
                                    {{ `Clase ${item.numero}` }}
                                </strong>
                            </h5>
                            <div style="display: flex; gap: 5px">
                                <div>
                                    <BBadge
                                        variant="light-warning"
                                        v-if="item.estado_clase == 'PENDIENTE'"
                                    >
                                        {{ item.estado_clase }}
                                    </BBadge>
                                    <BBadge
                                        variant="light-success"
                                        v-else-if="
                                            item.estado_clase == 'DESARROLLADA'
                                        "
                                    >
                                        {{ item.estado_clase }}
                                    </BBadge>
                                    <Button
                                        title="Asistencia"
                                        ref="btnSucces"
                                        class="btn btn-success btn-sm"
                                        @click.native.prevent="
                                            getAsistencia(item.id)
                                        "
                                    >
                                        <feather-icon
                                            icon="UserIcon"
                                            class="mr-20"
                                        />
                                        <span class="align-small"
                                            >Asistencia</span
                                        >
                                    </Button>
                                </div>
                                <div v-if="item.culminado != 'S'">
                                    ||
                                    <Button
                                        title="Editar"
                                        ref="btnEdit"
                                        class="btn btn-warning btn-sm btn-icon"
                                        @click.native.prevent="
                                            edit(item.id, index)
                                        "
                                    >
                                        <feather-icon
                                            icon="EditIcon"
                                            class="mr-20"
                                        />
                                    </Button>
                                    <Button
                                        title="Eliminar"
                                        ref="btnDestroy"
                                        class="btn btn-danger btn-sm btn-icon"
                                        @click.native.prevent="
                                            destroy(item.id, index)
                                        "
                                    >
                                        <feather-icon
                                            icon="Trash2Icon"
                                            class="mr-20"
                                        />
                                    </Button>
                                </div>
                            </div>
                        </template>
                        <BCardText>
                            <p class="indicador-text">
                                {{ item.descripcion }}
                            </p>
                            <section
                                style="display: flex; flex-direction: column"
                            >
                                <section
                                    style="
                                        display: flex;
                                        justify-content: start;
                                        align-items: center;
                                        gap: 5px;
                                    "
                                >
                                    <p>
                                        <strong>Fecha:</strong>
                                    </p>
                                    <p>
                                        <BBadge variant="light-success">
                                            {{
                                                convertDate(
                                                    item.fecha,
                                                    "-",
                                                    "/"
                                                )
                                            }}
                                        </BBadge>
                                    </p>
                                </section>
                                <section
                                    style="
                                        display: flex;
                                        align-items: center;
                                        gap: 5px;
                                    "
                                >
                                    <p>
                                        <strong>Hora:</strong>
                                    </p>
                                    <p>
                                        <BBadge variant="light-success">
                                            {{
                                                `${item.hora_inicio} - ${item.hora_fin}`
                                            }}
                                        </BBadge>
                                    </p>
                                </section>
                            </section>
                        </BCardText>
                    </BCard>
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
            <Modal ref="modalI" :loading="form.processing">
                <template #title>Clases</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Número
                                    <span class="required"> * </span></label
                                >

                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.numero"
                                    :class="{
                                        'is-invalid': errors.numero,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errors.numero" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Fecha
                                    <span class="required"> * </span></label
                                >
                                <input
                                    class="form-control"
                                    type="date"
                                    v-model="form.fecha"
                                    :class="{
                                        'is-invalid': errors.fecha,
                                    }"
                                />
                                <InputError :error="errors.fecha" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label
                                    >Descripción
                                    <span class="required"> * </span></label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.descripcion"
                                    :class="{
                                        'is-invalid': errors.descripcion,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Hora Inicio
                                    <span class="required"> * </span></label
                                >
                                <input
                                    class="form-control"
                                    type="time"
                                    v-model="form.hora_inicio"
                                    :class="{
                                        'is-invalid': errors.hora_inicio,
                                    }"
                                />
                                <InputError :error="errors.hora_inicio" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Hora Fin
                                    <span class="required"> * </span></label
                                >
                                <input
                                    class="form-control"
                                    type="time"
                                    v-model="form.hora_fin"
                                    :class="{
                                        'is-invalid': errors.hora_fin,
                                    }"
                                    @input="hourClassValidation"
                                />
                                <InputError :error="errors.hora_fin" />
                            </div>
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="close"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="store"
                        :disabled="form.processing"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Guardar</span>
                    </button>
                </template>
            </Modal>
            <Modal ref="modalEvaluacion">
                <template #title>Evaluación</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Nombre</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEvaluacion.nombre"
                                    :class="{
                                        'is-invalid': errors.descripcion,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Porcentaje</label>
                                <BInputGroup append="%">
                                    <BFormInput
                                        class="form-control"
                                        type="text"
                                        v-model="formEvaluacion.porcentaje"
                                    >
                                    </BFormInput>
                                </BInputGroup>

                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Semana</label>
                                <select
                                    class="form-control"
                                    v-model="formEvaluacion.semana"
                                    :class="{
                                        'is-invalid': errors.id_tipo_matricula,
                                    }"
                                >
                                    <option
                                        v-for="option in semanas"
                                        :key="option.id"
                                        :value="option.descripcion"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_tipo_matricula" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tipo de Evalución</label>
                                <select
                                    class="form-control"
                                    v-model="formEvaluacion.id_tipo_evaluacion"
                                    :class="{
                                        'is-invalid': errors.id_tipo_matricula,
                                    }"
                                >
                                    <option
                                        v-for="option in tipo_evaluacion"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_tipo_matricula" />
                            </div>
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="close"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeEvaluacion"
                        :disabled="form.processing"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Guardar</span>
                    </button>
                </template>
            </Modal>
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
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { convertDate } from "../../helpers.js";
import {
    BCard,
    BCardText,
    BFormInput,
    BInputGroup,
    BSpinner,
    BFormTimepicker,
    BBadge,
} from "bootstrap-vue";
import TableStudent from "../../Components/TableStudent.vue";
import TableCriterios from "./components/TableCriterios.vue";
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
        TableCriterios,
        BCard,
        BCardText,
        Modal,
        BFormInput,
        BInputGroup,
        BFormTimepicker,
    },
    name: "ClasesDocentes",
    props: {
        curso: Object,
        urlRoute: String,
        institutos: Array,
        secciones: Array,
        turnos: Array,
        clases: Array,
        periodo: Array,
        estudiantes: Array,
        uuid: String,
        culminado: Boolean,
    },
    data() {
        return {
            clasesView: [],
            loadingCursos: false,
            semanas: [
                {
                    id: 1,
                    descripcion: "SEMANA 1",
                    fecha_inicio: "12-02-2004",
                    fecha_fin: "19-02-2004",
                },
                {
                    id: 2,
                    descripcion: "SEMANA 2",
                    fecha_inicio: "20-02-2004",
                    fecha_fin: "27-02-2004",
                },
                {
                    id: 3,
                    descripcion: "SEMANA 3",
                    fecha_inicio: "28-02-2004",
                    fecha_fin: "3-02-2004",
                },
                {
                    id: 4,
                    descripcion: "SEMANA 4",
                    fecha_inicio: "4-02-2004",
                    fecha_fin: "-02-2004",
                },
                {
                    id: 5,
                    descripcion: "SEMANA 5",
                    fecha_inicio: "12-02-2004",
                    fecha_fin: "19-02-2004",
                },
                {
                    id: 6,
                    descripcion: "SEMANA 6",
                    fecha_inicio: "12-02-2004",
                    fecha_fin: "19-02-2004",
                },
                {
                    id: 7,
                    descripcion: "SEMANA 7",
                    fecha_inicio: "12-02-2004",
                    fecha_fin: "19-02-2004",
                },
                {
                    id: 8,
                    descripcion: "SEMANA 8",
                    fecha_inicio: "12-02-2004",
                    fecha_fin: "19-02-2004",
                },
            ],
            indicadores: [
                {
                    id: 1,
                    descripcion:
                        "FUNDAMENTOS FUNDAMENTALES PARA EL DESARROLLO DE APP WEB Y SERVIDORES EN LA NUBE",
                    semana_inicio: "SEMANA 1",
                    semana_fin: "SEMANA 3",
                },
                {
                    id: 2,
                    descripcion:
                        "FUNDAMENTOS NECESARIOS PARA EL DESARROLLO DE APP WEB Y SERVIDORES EN LA NUBE",
                    semana_inicio: "SEMANA 4",
                    semana_fin: "SEMANA 7",
                },
                // {
                //     id: 3,
                //     descripcion:
                //         "FUNDAMENTOS ACLARADOS PARA EL DESARROLLO DE APP WEB Y SERVIDORES EN LA NUBE",
                //     semana_inicio: "SEMANA 7",
                //     semana_fin: "SEMANA 12",
                // },
                // {
                //     id: 4,
                //     descripcion:
                //         "FUNDAMENTOS ESPECIALES PARA EL DESARROLLO DE APP WEB Y SERVIDORES EN LA NUBE",
                //     semana_inicio: "SEMANA 12",
                //     semana_fin: "SEMANA 16",
                // },
            ],
            //Table Evaluación
            evaluacion: [
                {
                    id: "1",
                    nombre: "TAREA I",
                    porcentaje: 0.2,
                    semana: "SEMANA 1",
                    id_tipo_evaluacion: 1,
                    id_indicador: 1,
                    indicador: "Indicador 1",
                },
                {
                    id: "2",
                    nombre: "PARTICIPACIONES I",
                    porcentaje: 0.3,
                    semana: "SEMANA 1",
                    id_tipo_evaluacion: 1,
                    id_indicador: 1,
                    indicador: "Indicador 1",
                },
                {
                    id: "3",
                    nombre: "EXÁMEN PARCIAL I",
                    porcentaje: 0.5,
                    semana: "SEMANA 1",
                    id_tipo_evaluacion: 1,
                    id_indicador: 1,
                    indicador: "Indicador 1",
                },
            ],
            tipo_evaluacion: [
                {
                    descripcion: "EXÁMEN SUSTITUTORIO",
                    id: 1,
                },
                {
                    descripcion: "CRITERIO",
                    id: 2,
                },
            ],
            evaluacionxIndicador: [],

            cursos: [],
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
                //¿?
                id: "-1",
                numero: "",
                descripcion: "",
                fecha: "",
                hora_inicio: "",
                hora_fin: "",
                id_curso_docente: "",
            }),
            formEvaluacion: useForm({
                //¿?
                id: "-1",
                nombre: "",
                porcentaje: "",
                semana: "",
                id_tipo_evaluacion: "",
                id_indicador: "",
                indicador: "",
            }),
            parents: [],
        };
    },
    created() {
        this.clasesView = this.clases;
        console.log("Clases:", this.clasesView);
    },
    mounted() {
        this.ordenarIndicadores(this.evaluacion);
    },
    methods: {
        convertDate,
        //Retrocedes
        remember() {
            this.$inertia.visit(this.routeTo(`gestion-cursos/${this.uuid}`));
        },
        //VALIDACIÓN DE NUMERO DE CLASE
        numberClassValidation() {
            for (let i = 0; i < this.clases.length; i++) {
                if (this.clases[i].numero == this.form.numero) {
                    alertWarning("El numero de clase debe ser único");
                    this.form.numero = "";
                    break;
                }
            }
        },
        hourClassValidation() {
            if (this.form.hora_inicio > this.form.hora_fin) {
                alertWarning(
                    "La hora de inicio de la clase debe ser mayor que la hora fin"
                );
                this.form.hora_inicio = "";
                this.form.hora_fin = "";
            }
        },
        //LLAMADO PARA LA ASISTENCIA
        getAsistencia(id) {
            this.$inertia.visit(
                this.routeTo(
                    `gestion-cursos/asistencia-alumnos/${this.curso.uuid}?id_clase_docente=${id}`
                )
            );
        },
        // LLAMADO PARA RECUPERAR LAS CLASES
        getClases() {
            this.$http
                .get(this.routeTo(`${this.urlRoute}${this.curso.id}`))
                .then((res) => {
                    this.clasesView = res.data;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {});
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
        createEvalucion(id) {
            this.$refs.modalEvaluacion.show();
            this.formEvaluacion.id_indicador = id;
            this.formEvaluacion.indicador = `Indicador ${id}`;
        },
        create() {
            this.form.reset();
            this.form.id_curso_docente = this.curso.id;
            let numero = 1;
            if (this.clasesView.length) {
                numero = this.clasesView[0].numero + 1;
            }
            this.form.numero = numero;
            this.$refs.modalI.show();
        },
        close() {
            this.$refs.modal.hide();
        },
        ordenarIndicadores(evaluaciones) {
            const agrupado = evaluaciones.reduce((acc, curr) => {
                const key = curr.id_indicador;
                if (!acc[key]) {
                    acc[key] = { indicador: curr.indicador, criterios: [] };
                }
                acc[key].criterios.push({
                    nombre: curr.nombre,
                    semana: curr.semana,
                    porcentaje: curr.porcentaje,
                });
                return acc;
            }, {});
            this.evaluacionxIndicador = Object.values(agrupado);
            console.log(this.evaluacionxIndicador);
        },
        storeEvaluacion() {
            this.formEvaluacion.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Clase Guardada Correctamente");
                    this.$refs.modalI.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                    this.getClases();
                });
        },

        store() {
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Clase Guardada Correctamente");
                    this.$refs.modalI.hide();
                    this.getClases();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
        },
        edit(id, index) {
            this.form.reset();
            this.$refs.btnEdit[index].load();
            this.$http
                .get(this.routeTo(`${this.urlRoute}${id}/edit`))
                .then((res) => {
                    this.setForm(res.data);
                    this.$refs.modalI.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit[index].reset();
                });
        },
        destroy(id, index) {
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    this.$refs.btnDestroy[index].load();
                    this.$http
                        .delete(this.routeTo(`${this.urlRoute}${id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$refs.datatable.reload();
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.$refs.btnDestroy[index].reset();
                            this.getClases();
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
.required {
    color: red;
    margin-left: 2px;
}
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
.card {
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1) !important;
    transition: all 0s ease-in-out, background 0s, color 0s, border-color 0.1s,
        transform 0.5s;
    border-bottom: 3px solid #28c76f !important;
    transform: translateY(0);
}
.card:hover {
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.14) !important;
    border-bottom: 4px solid #28c76f !important;
    transform: translateY(-5px);
}
.card-body {
    padding-bottom: 0rem !important;
}
.card-indicadores {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}
.header-indicador {
    .feather {
        height: 2.5vh;
    }
}
.indicador-text {
    margin-top: 10px;
    display: flex;
    font-size: 17px;
    font-weight: bold;
}
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
.cursos .card {
    border: 1px solid black !important;
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
