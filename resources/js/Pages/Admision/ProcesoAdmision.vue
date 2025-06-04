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
                    <Button
                        ref="btnEdit"
                        class="btn btn-success"
                        @click.native.prevent="viewRequisitos"
                    >
                        <feather-icon icon="AlignRightIcon" class="mr-50" />
                        <span class="align-middle">Modalidades</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                Administrar Proecesos de Admisión
                            </h4>
                        </template>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
            <!-- MODAL PARA VISUALIZAR LOS REQUSITOS POR MODALIDAD -->
            <ModalTable ref="modal1" :key="url">
                <template #title>Modalidades</template>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <button
                            class="btn btn-primary"
                            @click.prevent="createRequisitos"
                        >
                            <feather-icon icon="PlusIcon" class="mr-50" />
                            <span class="align-middle">Nuevo</span>
                        </button>
                        <Button
                            ref="btnDestroy"
                            class="btn btn-danger"
                            @click.native.prevent="destroyDetalle"
                        >
                            <feather-icon icon="Trash2Icon" class="mr-50" />
                            <span class="align-middle">Eliminar</span>
                        </Button>
                        <DataTable
                            ref="datatableI"
                            :columns="columnsRequisitos"
                            :path="routeTo(`${this.url}`)"
                        />
                    </div>
                </div>
            </ModalTable>
            <!-- MODAL PARA AGREGAR REQUISITOS -->
            <Modal ref="modal2" :loading="formI.processing">
                <template #title>Modalidades</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Modalidad Admisions</label>
                                <select
                                    class="form-control"
                                    v-model="formI.id_modalidad_admision"
                                    :class="{
                                        'is-invalid':
                                            errorsI.id_modalidad_admision,
                                    }"
                                >
                                    <option
                                        v-for="option in tipo_modalidades"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError
                                    :error="errorsI.id_modalidad_admision"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Fecha de Exámen</label>
                                <BFormDatepicker
                                    class="form-control"
                                    type="text"
                                    v-model="formI.fecha_examen"
                                    :class="{
                                        'is-invalid': errorsI.fecha_examen,
                                    }"
                                    :date-format-options="{
                                        year: 'numeric',
                                        month: 'numeric',
                                        day: 'numeric',
                                    }"
                                />
                                <InputError :error="errorsI.fecha_examen" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Hora del Exámen</label>
                                <div>
                                    <BFormTimepicker
                                        v-model="formI.hora_examen"
                                        locale="en"
                                    ></BFormTimepicker>
                                </div>
                                <InputError :error="errorsI.hora_examen" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Vacantes</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formI.vacantes"
                                    :class="{
                                        'is-invalid': errorsI.vacantes,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errorsI.vacantes" />
                            </div>
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeII"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeRequisitos"
                        :disabled="form.processing"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Guardar</span>
                    </button>
                </template>
            </Modal>
            <Modal ref="modal" :loading="form.processing">
                <template #title>Proceso de Admisión</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción</label>
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
                                <label>Fecha de Inicio</label>
                                <BFormDatepicker
                                    class="form-control"
                                    type="text"
                                    v-model="form.fecha_inicio"
                                    :class="{
                                        'is-invalid': errors.fecha_inicio,
                                    }"
                                    :date-format-options="{
                                        year: 'numeric',
                                        month: 'numeric',
                                        day: 'numeric',
                                    }"
                                />
                                <InputError :error="errors.fecha_inicio" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Fecha de Fin</label>
                                <BFormDatepicker
                                    class="form-control"
                                    type="text"
                                    v-model="form.fecha_fin"
                                    :class="{
                                        'is-invalid': errors.fecha_fin,
                                    }"
                                    :date-format-options="{
                                        year: 'numeric',
                                        month: 'numeric',
                                        day: 'numeric',
                                    }"
                                />
                                <InputError :error="errors.fecha_fin" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Puntaje Mínimo</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.puntaje_minimo"
                                    :class="{
                                        'is-invalid': errors.puntaje_minimo,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errors.puntaje_minimo" />
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
import Switch from "../../Components/Switch.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId, routeTo } from "../../helpers.js";
import ModalTable from "../../Components/ModalTable.vue";
import {
    BFormCheckbox,
    BFormCheckboxGroup,
    BFormDatepicker,
    BFormTimepicker,
} from "bootstrap-vue";
import VueSelect from "vue-select";

export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        ModalTable,
        Switch,
        BFormCheckbox,
        BFormDatepicker,
        VueSelect,
        BFormTimepicker,
    },
    name: "Module",
    props: {
        urlRoute: String,
        tipo_modalidades: Array,
    },
    data() {
        return {
            view: false,
            columnsRequisitos: [
                {
                    data: null,
                    title: "#",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                },
                { data: "modalidades", title: "Descripcion" },
                { data: "fecha_examen", title: "Fecha" },
                { data: "hora_examen", title: "Hora" },
                { data: "vacantes", title: "Vacantes" },
            ],
            columns: [
                {
                    data: null,
                    title: "#",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return meta.row + 1;
                    },
                },

                { data: "descripcion", title: "Descripcion" },
                { data: "fecha_inicio", title: "Fecha Inicio" },
                { data: "fecha_fin", title: "Fecha Fin" },
                { data: "puntaje_minimo", title: "Puntaje Mínimo" },
            ],
            form: useForm({
                id: "-1",
                fecha_inicio: "",
                fecha_fin: "",
                puntaje_minimo: "",
                descripcion: "",
            }),
            formI: useForm({
                id: "-1",
                id_modalidad_admision: "",
                id_proceso_admision: "",
                fecha_examen: "",
                hora_examen: "",
                vacantes: "",
            }),
            parents: [],
            id_proceso: "1",
            reload: "",
            url: `${this.urlRoute}/datatables_detalle/1`,
        };
    },

    methods: {
        prueba() {
            console.log("hola");
        },
        setForm(data) {
            this.setFormData(this.form, data);
        },
        reset(){
            this.form.reset();
            this.clearErrors();
        },
        create() {
            this.reset();
            this.form.id_instituto = firstId(this.institutos);
            this.$refs.modal.show();
        },
        closeII() {
            this.$refs.modal2.hide();
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
                headers: {
                    "X-Inertia-Error-Bag": "process",
                },
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
        },
        viewRequisitos() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.id_proceso = row.id;
            this.url = `${this.urlRoute}/datatables_detalle/${this.id_proceso}`;
            this.$nextTick(() => {
                this.$refs.datatableI.reload();
                this.$refs.modal1.show();
            });
        },
        createRequisitos() {
            this.formI.reset();
            this.$refs.modal2.show();
            console.log(this.url);
            console.log(this.id_modalidad);
        },
        storeRequisitos() {
            this.formI.processing = true;
            this.formI.id_proceso_admision = this.id_proceso;
            this.$http({
                method: "POST",
                url: this.routeTo(`procesos_detalle`),
                data: this.formI.data(),
                headers: {
                    "X-Inertia-Error-Bag": "process_detalle",
                },
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatableI.reload();
                    this.formI.processing = false;
                    this.$refs.modal2.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.formI.processing = false;
                });
        },
        edit() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.reset();
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
        destroyDetalle() {
            const row = this.$refs.datatableI.getSelectedRow();
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
                        .delete(this.routeTo(`procesos_detalle/${row.id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$refs.datatableI.reload();
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.$refs.btnDestroy.reset();
                        });
                }
            );
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
            return this.$page.props.errors.process || {};
        },
        errorsI() {
            return this.$page.props.errors.process_detalle || {};
        },
    },
};
</script>
<style scoped>
.b-form-btn-label-control.form-control {
    align-items: center;
}
</style>
