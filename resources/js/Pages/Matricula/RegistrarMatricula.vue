<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <button class="btn btn-primary" @click.prevent="create">
                        <feather-icon icon="PlusIcon" class="mr-50" />
                        <span class="align-middle">Nuevo</span>
                    </button>
                    <Button
                        ref="btnEdit"
                        class="btn btn-warning"
                        @click.native.prevent="edit"
                    >
                        <feather-icon icon="EditIcon" class="mr-50" />
                        <span class="align-middle">Modificar</span>
                    </Button>
                    <Button
                        ref="btnDestroy"
                        class="btn btn-danger"
                        @click.native.prevent="destroy"
                    >
                        <feather-icon icon="Trash2Icon" class="mr-50" />
                        <span class="align-middle">Eliminar</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">Registrar Matricula</h4>
                        </template>
                        <div class="row">
                            <div class="col-md-4 col-lg-4">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="form-group">
                                            <label>Sedes</label>
                                            <select
                                                class="form-control"
                                                v-model="id_sede"
                                                @change="getRouteTable"
                                            >
                                                <option value="0" disabled>
                                                    Selecciona una sede
                                                </option>
                                                <option
                                                    v-for="sede in sedes"
                                                    :key="sede.id"
                                                    :value="sede.id"
                                                >
                                                    {{ sede.descripcion }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="route_table"
                        />
                    </Card>
                </div>
            </div>
            <Modal ref="modal" :loading="form.processing" modal="modal-lg">
                <template #title>Matricula {{ fullNameStudent }}</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Periodo de Clases
                                    <span style="color: red; margin-left: 2px">
                                        *
                                    </span>
                                </label>
                                <select
                                    @change="updateEstudiante(), resetItem()"
                                    class="form-control"
                                    v-model="form.id_periodo_clases"
                                    :class="{
                                        'is-invalid': errors.id_periodo_clases,
                                    }"
                                >
                                    <option value="0">SELECCIONE</option>
                                    <option
                                        v-for="option in periodo_clases"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_periodo_clases" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Tipo de Matricula</label>
                                <select
                                    @change="updateEstudiante"
                                    class="form-control"
                                    v-model="form.id_tipo_matricula"
                                    :class="{
                                        'is-invalid': errors.id_tipo_matricula,
                                    }"
                                >
                                    <option
                                        v-for="option in tipo_matricula"
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
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Turno</label>
                                <select
                                    @change="updateEstudiante(), resetItem()"
                                    class="form-control"
                                    v-model="form.id_turno"
                                    :class="{
                                        'is-invalid': errors.id_turno,
                                    }"
                                >
                                    <option
                                        v-for="option in turnos"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Sección
                                    <span style="color: red; margin-left: 2px">
                                        *
                                    </span></label
                                >
                                <select
                                    @change="updateEstudiante(), resetItem()"
                                    class="form-control"
                                    v-model="form.id_seccion"
                                    :class="{
                                        'is-invalid': errors.id_seccion,
                                    }"
                                >
                                    <option
                                        v-for="option in secciones"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-9 col-lg-9">
                            <SelectStudent
                                ref="estudiantes"
                                :errors="errors.id_estudiante"
                                @input="onEstudiante"
                                :disabled="form.id != '-1'"
                            />
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <label id="label-curso">&nbsp;</label>
                                <Button
                                    @click.native.prevent="onCursos"
                                    ref="btnCurso"
                                    class="btn btn-primary"
                                    style="
                                        display: block;
                                        padding: 0.786rem 0.8rem;
                                    "
                                >
                                    <feather-icon icon="SearchIcon" />
                                    <span>Consultar</span>
                                </Button>
                            </div>
                        </div>
                    </div>
                </form>
                <SelectTable
                    :items="items"
                    :estudiantes="dataEstudiante"
                    @sendMessage="receiveFromChild"
                ></SelectTable>
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
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { currentDate, firstId, routeTo } from "../../helpers.js";
import SelectStudent from "./Components/SelectStudent.vue";
import SelectTable from "../../Components/SelectTable.vue";
export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        SelectStudent,
        SelectTable,
    },
    name: "Module",
    props: {
        urlRoute: String,
        tipo_proceso: Array,
        tipo_matricula: Array,
        turnos: Array,
        secciones: Array,
        periodo_clases: Array,
        sedes: Array,
    },
    data() {
        return {
            dataEstudiante: {},
            fullNameStudent: "",
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

                { data: "numero_documento", title: "N° Documento" },
                { data: "estudiante", title: "Estudiante" },
                { data: "programa_estudio", title: "Programa de Estudio" },
                { data: "plan_estudio", title: "Plan de Estudio" },
                { data: "semestre", title: "Semestre" },
                { data: "ciclo", title: "Ciclo" },
                { data: "turno", title: "Turno" },
                { data: "tipo_matricula", title: "Tipo Matricula" },
            ],
            form: useForm({
                id: "-1",
                id_tipo_proceso_matricula: 1,
                id_tipo_matricula: 1,
                id_estudiante: 0,
                id_periodo_clases: 0,
                id_turno: 1,
                id_seccion: 1,
                id_ciclo: "",
                fecha_matricula: "",
                id_cursos_docente: "",
            }),
            id_sede: 0,
            route_table: "",
            parents: [],
            items: [],
        };
    },
    mounted() {
        this.getRouteTable();
    },
    methods: {
        resetItem() {
            this.items = [];
        },
        receiveFromChild(message) {
            console.log(message);
            this.form.id_cursos_docente = message;
        },
        updateEstudiante() {
            this.dataEstudiante.id_seccion = this.form.id_seccion;
            this.dataEstudiante.id_turno = this.form.id_turno;
            this.dataEstudiante.id_periodo_clases = this.form.id_periodo_clases;
            console.log(this.dataEstudiante.id_periodo_clases);
        },
        onEstudiante(estudiante) {
            if (estudiante.id === undefined) {
                return;
            }
            this.dataEstudiante = estudiante;
            this.form.id_estudiante = estudiante.id;
            this.form.id_ciclo = estudiante.id_ciclo;
            this.dataEstudiante.id_seccion = this.form.id_seccion;
            this.dataEstudiante.id_turno = this.form.id_turno;
            this.dataEstudiante.id_periodo_clases = this.form.id_periodo_clases;
            this.dataEstudiante.id_sede == estudiante.id_sede;
            console.log(estudiante);
        },
        setForm(data) {
            this.setFormData(this.form, data);
            if (data.estudiante) {
                this.$refs.estudiantes.setData(data.estudiante);
                this.fullNameStudent = data.estudiante.apellidos_completos;
            }
            if (data.cursos_matricula) {
                this.items = data.cursos_matricula;
            }
        },
        create() {
            this.form.reset();
            this.$refs.estudiantes.clean();
            this.form.fecha_matricula = currentDate();
            this.resetItem();
            this.$refs.modal.show();
        },
        close() {
            this.$refs.modal.hide();
        },
        store() {
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal.hide();
                })
                .catch((error) => {
                    if (error.response.data.errors.id_cursos_docente) {
                        alertWarning(
                            "Tienes que seleccionar cursos para la matricula"
                        );
                    }
                })
                .finally(() => {
                    this.form.processing = false;
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
                    console.log(res.data);
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
        onCursos() {
            if (this.form.id_periodo_clases == 0) {
                alertWarning("Seleccione periodo de clases");
                return;
            }
            if (
                this.form.id_estudiante == 0 ||
                this.form.id_estudiante == undefined
            ) {
                alertWarning("Buscar estudiante");
                return;
            }

            this.$refs.btnCurso.load();
            this.$http({
                method: "POST",
                url: this.routeTo(`matriculas/searchCursos`),
                data: this.dataEstudiante,
            })
                .then((res) => {
                    if (!res.data.estadoMatricula) {
                        this.items = res.data;
                    } else {
                        alertWarning(
                            "El estudiante ya tiene una matricula registrada en este periodo de clases"
                        );
                        return;
                    }
                    this.addCheckArray();
                    //alertSuccess("Datos Guardados Correctamente");
                })
                .catch((error) => {})
                .finally(() => {
                    this.$refs.btnCurso.reset();
                });
        },
        addCheckArray() {
            this.items = this.items.map((item) => {
                return { ...item, check: "" };
            });
            console.log(this.items);
        },
        getRouteTable() {
            this.route_table = routeTo(
                `${this.urlRoute}/datatables/${this.id_sede}`
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
#label-curso {
    display: none;
}
@media (min-width: 768px) {
    #label-curso {
        display: block;
    }
}
</style>
