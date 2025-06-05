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
                        <span class="align-middle">Requisitos</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                Administrar Modalidades de Admisión
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
                <template #title>Requisitos</template>
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
                            @click.native.prevent="destroyRequisito"
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
                <template #title>Requisito</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripcion</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formI.descripcion"
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
                                <label>Obligatorio</label>
                                <BFormCheckbox
                                    v-model="formI.obligatorio"
                                    name="check-button"
                                    switch
                                >
                                </BFormCheckbox>
                                <InputError :error="errors.descripcion" />
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
                <template #title>Modalidad Admisión</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tipo de Modalidad</label>
                                <select
                                    class="form-control"
                                    v-model="form.tipo"
                                    :class="{
                                        'is-invalid': errors.tipo,
                                    }"
                                >
                                    <option value="ORDIANRIO">ORDIANRIO</option>
                                    <option value="EXONERADO">EXONERADO</option>
                                    <option value="EXTRAORDINARIO">
                                        EXTRAORDINARIO
                                    </option>
                                </select>
                                <InputError :error="errors.tipo" />
                            </div>
                        </div>
                    </div>
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
import { BFormCheckbox, BFormCheckboxGroup } from "bootstrap-vue";

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
    },
    name: "Module",
    props: {
        urlRoute: String,
        institutos: Array,
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
                { data: "descripcion", title: "Descripcion" },
            ],
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
                { data: "tipo", title: "Tipo Modalidad" },
                { data: "descripcion", title: "Descripcion" },
            ],
            form: useForm({
                id: "-1",
                tipo: "",
                descripcion: "",
            }),
            formI: useForm({
                id: "-1",
                id_modalidades_admision: 0,
                descripcion: "",
                obligatorio: "",
            }),
            parents: [],
            id_modalidad: "1",
            reload: "",
            url: `${this.urlRoute}/datatables/requisitos/1`,
        };
    },

    methods: {
        prueba() {
            console.log("hola");
        },
        setForm(data) {
            this.setFormData(this.form, data);
        },
        reset() {
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
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal1.hide();
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
            this.id_modalidad = row.id;
            this.url = `${this.urlRoute}/datatables/requisitos/${this.id_modalidad}`;
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
            this.formI.id_modalidades_admision = this.id_modalidad;
            this.formI.processing = true;
            console.log(this.formI);
            this.$http({
                method: "POST",
                url: this.routeTo(`requisitos_admision`),
                data: this.formI.data(),
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
        destroyRequisito() {
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
                        .delete(this.routeTo(`requisitos_admision/${row.id}`))
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
        // url() {
        //     console.log(this.id_modalidad);
        //     return this.routeTo(
        //         `${this.urlRoute}/datatables/requisitos/${this.id_modalidad}`
        //     );
        // },
        errors() {
            return this.$page.props.errors || {};
        },
    },
};
</script>
