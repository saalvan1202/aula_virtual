<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <button
                        class="btn btn-primary"
                        @click.prevent="redirect('create')"
                    >
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
                            <h4 class="card-title">Bolsa Laboral</h4>
                        </template>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import VSelect from "vue-select";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
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
                { data: "empresa", title: "Empresa" },
                { data: "programa_estudio", title: "Programa de estudios" },
                { data: "publicacion", title: "Publicación" },
                { data: "email", title: "Correo" },
                { data: "ubicacion", title: "Ubicación" },
                { data: "puesto", title: "Puesto" },
                { data: "horario", title: "Horario" },
            ],
        };
    },
    mounted() {},
    methods: {
        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${this.urlRoute}/${name}`));
            //console.log(`${this.urlRoute}/${name}`);
        },
        edit() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }

            this.redirect(`edit/${row.id}`);
        },
        destroy() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            confirm(
                {
                    text: "¿Desea eliminar el registro seleccionado?",
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
