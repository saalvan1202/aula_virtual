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
                            <h4 class="card-title">Administrar Usuarios</h4>
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
                <template #title>Usuario</template>
                <form class="form form-vertical">
                    <FormPerson :form="form" :errors="errors" />
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Correo</label>
                                <input
                                    class="form-control"
                                    type="email"
                                    v-model="form.email"
                                    :class="{ 'is-invalid': errors.email }"
                                />
                                <InputError :error="errors.email" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Telefono</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.telefono"
                                    :class="{ 'is-invalid': errors.telefono }"
                                />
                                <InputError :error="errors.telefono" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Tipo Contrato</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_tipo_contrato"
                                    :class="{
                                        'is-invalid': errors.id_tipo_contrato,
                                    }"
                                >
                                    <option
                                        v-for="option in tipo_contratos"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_tipo_contrato" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Perfil</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_perfil"
                                    :class="{ 'is-invalid': errors.id_perfil }"
                                >
                                    <option
                                        v-for="option in perfiles"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.descripcion }}
                                    </option>
                                </select>
                                <InputError :error="errors.id_perfil" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Usuario</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.usuario"
                                    :class="{ 'is-invalid': errors.usuario }"
                                />
                                <InputError :error="errors.usuario" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Contraseña</label>
                                <input
                                    class="form-control"
                                    type="password"
                                    v-model="form.password"
                                    :class="{ 'is-invalid': errors.password }"
                                />
                                <InputError :error="errors.password" />
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
import FormPerson from "../../Components/FormPerson.vue";
export default {
    components: {
        FormPerson,
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
    },
    name: "User",
    props: {
        urlRoute: String,
        perfiles: Array,
        tipo_contratos: Array,
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
                    width: "2rem",
                },
                { data: "nombres", title: "Nombres" },
                { data: "apellido_paterno", title: "Apellido Paterno" },
                { data: "apellido_materno", title: "Apellido Materno" },
                { data: "tipo_documento", title: "Documento" },
                { data: "numero_documento", title: "Numero" },
                { data: "perfil", title: "Perfil" },
            ],
            form: useForm({
                id: "-1",
                id_tipo_documento_identidad: 0,
                numero_documento: "",
                nombres: "",
                apellido_paterno: "",
                apellido_materno: "",
                id_perfil: 2,
                usuario: "",
                password: "",
                sexo: "M",
                estado_civil: "SOLTERO",
                telefono: "",
                email: "",
                id_tipo_contrato: 0,
                id_persona: "-1",
            }),
        };
    },
    methods: {
        onDni(data) {
            this.form.nombres = data.nombres;
            this.form.apellido_paterno = data.apellido_paterno;
            this.form.apellido_materno = data.apellido_materno;
        },
        setForm(data) {
            this.setFormData(this.form, data);
            this.form.password = data.password_vista;
        },
        create() {
            this.form.reset();
            this.clearErrors();
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
            this.form.reset();
            this.clearErrors();
            this.$refs.btnEdit.load();
            this.$http
                .get(this.routeTo(`${this.urlRoute}/${row.id}/edit`))
                .then((res) => {
                    this.setForm(res.data);
                    this.$refs.modalProfile.show();
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
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
                        .catch((error) => {
                            if (this.errors.record != undefined) {
                                alertWarning(this.errors.record);
                            }
                        })
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
