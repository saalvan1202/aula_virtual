<template>
    <LayoutContent>
        <section>
            <!-- <div class="row">
                <div class="rows col-md-12 col-lg-12">
                    <button class="btn btn-primary" @click.prevent="create">
                        <feather-icon icon="PlusIcon" class="mr-50" />
                        <span class="align-middle">Nueva Modalidad</span>
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
            </div> -->
            <div class="row mt-1">
                <div class="col-12">
                    <section class="options">
                        <h4 class="header">Modalidades</h4>
                        <section class="options_admision">
                            <div class="row">
                                <div>
                                    <div class="form-group">
                                        <label>Tipo de Modalidad</label>
                                        <select
                                            class="form-control"
                                            v-model="filtroModalidad"
                                            @change="obtenerModalidad"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_instituto,
                                            }"
                                        >
                                            <option value="ORDINARIO">
                                                ORDIANRIO
                                            </option>
                                            <option value="EXONERADO">
                                                EXONERADO
                                            </option>
                                            <option value="EXTRAORDINARIO">
                                                EXTRAORDINARIO
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_instituto"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div>
                                    <div class="form-group">
                                        <label>Modalidad</label>
                                        <select
                                            class="form-control"
                                            v-model="form.id_instituto"
                                            :class="{
                                                'is-invalid':
                                                    errors.id_instituto,
                                            }"
                                        >
                                            <option
                                                v-for="option in modalidades"
                                                :key="option.id"
                                                :value="option.id"
                                            >
                                                {{ option.descripcion }}
                                            </option>
                                        </select>
                                        <InputError
                                            :error="errors.id_instituto"
                                        />
                                    </div>
                                </div>
                            </div>
                        </section>
                    </section>
                    <Card>
                        <template #header>
                            <h4 class="card-title">Requisitos</h4>
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
                <template #title>Requisito</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Modalidad Admision</label>
                                <select
                                    class="form-control"
                                    v-model="form.id_instituto"
                                    :class="{
                                        'is-invalid': errors.id_instituto,
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
                                <InputError :error="errors.id_instituto" />
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
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Obligatorio</label>
                                <input
                                    class="form-control"
                                    type="checkbox"
                                    v-model="form.OBLIGATO"
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
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
export default {
    components: { LayoutContent, Card, DataTable, Modal, InputError, Button },
    name: "Module",
    props: {
        urlRoute: String,
        tipo_modalidades: Array,
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
                { data: "modalidad", title: "Modalidad" },
                { data: "descripcion", title: "Descripcion" },
                { data: "obligatorio", title: "Obligatorio" },
            ],
            form: useForm({
                id: "-1",
                id_modalidades_admision: 0,
                descripcion: "",
                obligatorio: "",
            }),
            parents: [],
            filtroModalidad: "",
            modalidades: "",
        };
    },
    mounted() {},
    methods: {
        async obtenerModalidad() {
            if (this.filtroModalidad) {
                const response = await this.$http({
                    method: "GET",
                    url: this.routeTo(
                        `${this.urlRoute}/filterModalidad/${this.filtroModalidad}`
                    ),
                });
                console.log(response.data);
                this.modalidades = response.data;
            }
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
    },
    computed: {
        errors() {
            return this.$page.props.errors || {};
        },
    },
};
</script>
<style scoped>
.rows {
    display: flex;
    justify-content: end;
}
.options {
    background-color: white;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1); /* Agrega el box-shadow */
    border-radius: 10px;
    margin-bottom: 10px !important;
}
.header {
    padding: 1.5rem;
    padding-bottom: 0;
    margin: 0;
}
.options_admision {
    display: grid;
    justify-content: center;
    align-items: center;
    grid-template-rows: 1fr 1fr;
    height: 25vh;
    padding-top: 0px !important;

    select {
        width: 20vw;
    }
    .card-title {
        display: flex !important;
        justify-content: start !important;
        margin-top: 10px;
    }
}
</style>
