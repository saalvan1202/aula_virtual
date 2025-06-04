<template>
    <LayoutContent>
        <section>
            <!-- <div class="row">
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
            </div> -->
            <button class="btn btn-warning" @click.prevent="editNotas">
                <feather-icon icon="EditIcon" class="mr-50" />
                <span class="align-middle">Calificar</span>
            </button>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">Notas</h4>
                        </template>
                        <TableStudent
                            :alumnos="alumnos"
                            :BDisable="BDisable"
                            :CInpunt="CInpunt"
                            @sendNotes="changeNotas"
                            @GDisable="changeGDisable"
                        />
                    </Card>
                    <section class="footer-button">
                        <button
                            class="btn btn-success"
                            @click.prevent="store"
                            :disabled="GDisable"
                        >
                            <feather-icon icon="SaveIcon" />
                            <span>Guardar</span>
                        </button>
                    </section>
                </div>
            </div>
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
import TableStudent from "../../Components/TableStudent.vue";

export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        TableStudent,
    },
    name: "Module",
    props: {
        urlRoute: String,
        institutos: Array,
        alumnos: Array,
        uuid: String,
        id_curso_docente: String,
    },
    data() {
        return {
            GDisable: false,
            BDisable: true,
            CInpunt: "inputD",
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
                id: "-1",
                id_instituto: 0,
                descripcion: "",
            }),
            parents: [],
            notas: [],
        };
    },
    mounted() {},
    methods: {
        changeGDisable(state) {
            this.GDisable = state;
        },
        changeNotas(notasAlumno) {
            this.notas = notasAlumno;
            console.log(this.notas);
        },
        editNotas() {
            this.BDisable = false;
            this.CInpunt = "inputE";
        },
        setAlumnos() {
            console.log(this.alumnos);
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
            if (this.BDisable == true) {
                alertWarning("No hay calificaciones por guardar");
            } else {
                this.BDisable = true;
                this.$http({
                    method: "POST",
                    url: this.routeTo(`${this.urlRoute}`),
                    data: this.notas,
                })
                    .then((res) => {
                        console.log(this.alumnos[0].uuid);
                        alertSuccess("Notas Guardadas Correctamente");
                        this.$inertia.visit(
                            this.routeTo(
                                `${this.urlRoute}/nota/?uuid=${this.uuid}&id=${this.id_curso_docente}`
                            )
                        );
                    })
                    .catch((error) => {})
                    .finally(() => {
                        this.form.processing = false;
                        this.BDisable = true;
                    });
            }
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
<style>
.footer-button {
    display: flex;
    justify-content: end;
}
</style>
