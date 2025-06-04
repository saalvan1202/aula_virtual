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
                            <h4 class="card-title">Administrar Ambientes</h4>
                        </template>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
            <Modal ref="modalProfile" :loading="form.processing">
                <template #title>Ambiente</template>
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
                                <label>Piso</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.piso"
                                    :class="{ 'is-invalid': errors.piso }"
                                    v-input-upper
                                />
                                <InputError :error="errors.piso" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Aforo</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.aforo"
                                    :class="{ 'is-invalid': errors.aforo }"
                                    v-input-upper
                                />
                                <InputError :error="errors.aforo" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ubicación</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.ubicacion"
                                    :class="{ 'is-invalid': errors.ubicacion }"
                                    v-input-upper
                                />
                                <InputError :error="errors.ubicacion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Tipo Aula</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_tipo_aula"
                                    :class="{
                                        'is-invalid': errors.id_tipo_aula,
                                    }"
                                >
                                    <option
                                        v-for="option in tipo_ambiente"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_tipo_aula" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Sede</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_sede"
                                    :class="{
                                        'is-invalid': errors.id_sede,
                                    }"
                                >
                                    <option
                                        v-for="option in sedes"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_sede" />
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
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
export default {
    components: { LayoutContent, Card, DataTable, Modal, InputError, Button },
    name: "Institute",
    props: {
        urlRoute: String,
        sedes: Array,
        tipo_ambiente: Array,
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
                { data: "descripcion", title: "Descripción" },
                { data: "sedes", title: "Sede" },
                { data: "tipo_aulas", title: "Tipo Aula" },
                { data: "piso", title: "Piso" },
                { data: "ubicacion", title: "Ubicación" },
                { data: "aforo", title: "Aforo" },
            ],
            form: useForm({
                id: "-1",
                id_sede: "",
                descripcion: "",
                id_tipo_aula: "",
                piso: "",
                ubicacion: "",
                aforo: "",
            }),
        };
    },
    methods: {
        setForm(data) {
            this.setFormData(this.form, data);
        },
        reset(){
            this.form.reset();
            this.clearErrors();
        },
        create() {
            this.reset();
            this.$refs.modalProfile.show();
        },
        close() {
            this.$refs.modalProfile.hide();
        },
        store() {
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(this.urlRoute),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modalProfile.hide();
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
                    this.$refs.modalProfile.show();
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
