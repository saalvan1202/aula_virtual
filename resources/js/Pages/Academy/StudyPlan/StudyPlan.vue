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
                    ||
                    <Button
                        ref="btnModule"
                        class="btn btn-dark"
                        @click.native.prevent="module"
                    >
                        <feather-icon icon="ListIcon" class="mr-50" />
                        <span class="align-middle">Modulos</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                Administrar Plan de estudios
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
            <Modal ref="modal" :loading="form.processing">
                <template #title>Plan estudio</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripcion</label>
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
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Modalidad</label>
                                <!--<input class="form-control" type="text" v-model="form.modalidad"
                                       :class="{'is-invalid':errors.modalidad}"
                                       v-input-upper
                                />-->
                                <select
                                    class="form-control"
                                    v-model="form.modalidad"
                                    :class="{ 'is-invalid': errors.modalidad }"
                                >
                                    <option value="">SELECCIONE</option>
                                    <option value="PRESENCIAL">
                                        PRESENCIAL
                                    </option>
                                    <option value="SEMI-PRESENCIAL">
                                        SEMI-PRESENCIAL
                                    </option>
                                </select>
                                <InputError :error="errors.modalidad" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Programa Estudios</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_programa_estudio"
                                    :class="{
                                        'is-invalid':
                                            errors.id_programa_estudio,
                                    }"
                                >
                                    <option value="0">SELECCIONE</option>
                                    <option
                                        v-for="option in programa_estudios"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError
                                    :error="errors.id_programa_estudio"
                                />
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
import VSelect from "vue-select";
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import Card from "../../../Components/Card.vue";
import DataTable from "../../../Components/DataTable.vue";
import Modal from "../../../Components/Modal.vue";
import InputError from "../../../Components/InputError.vue";
import Button from "../../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../../sweetAlert2.js";
import { firstId } from "../../../helpers.js";
export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        VSelect,
    },
    name: "StudyPlan",
    props: {
        urlRoute: String,
        programa_estudios: Array,
    },
    data() {
        return {
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
                { data: "descripcion", title: "Nombre" },
                { data: "programa_estudios", title: "Programa Estudio" },
                { data: "modalidad", title: "Modalidad" },
            ],
            form: useForm({
                id: "-1",
                id_programa_estudio: 0,
                descripcion: "",
                modalidad: "",
            }),
        };
    },
    mounted() {},
    methods: {
        setForm(data) {
            this.setFormData(this.form, data);
        },
        reset() {
            this.form.reset();
            this.clearErrors();
        },
        create() {
            this.reset();
            //this.form.id_instituto=firstId(this.institutos)
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
                .catch((error) => {})
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
        module() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.$inertia.visit(
                this.routeTo(`${this.urlRoute}/${row.id}/modulos`)
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
