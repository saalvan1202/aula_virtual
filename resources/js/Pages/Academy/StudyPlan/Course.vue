<template>
    <section>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <button class="btn btn-sm btn-primary" @click.prevent="create">
                    <feather-icon icon="PlusIcon" class="mr-50" />
                    <span class="align-middle">Nuevo</span>
                </button>
                <Button
                    ref="btnEdit"
                    class="btn btn-sm btn-warning"
                    @click.native.prevent="edit"
                >
                    <feather-icon icon="EditIcon" class="mr-50" />
                    <span class="align-middle">Modificar</span>
                </Button>
                <Button
                    ref="btnDestroy"
                    class="btn btn-sm btn-danger"
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
                        <h4 class="card-title">
                            Administrar Unidades Didácticas
                        </h4>
                    </template>
                    <DataTable
                        :height="200"
                        ref="datatable"
                        :columns="columns"
                        :path="
                            routeTo(
                                `${this.urlRoute}/${this.plan.id}/datatables`
                            )
                        "
                    />
                </Card>
            </div>
        </div>
        <Modal ref="modal" :loading="form.processing">
            <template #title>Unidad Didáctica</template>
            <form class="form form-vertical">
                <SelectCompetence
                    ref="competencia"
                    :errors="errors.id_modulo_competencia"
                    @input="onCompetencia"
                    :id-plan="plan.id"
                />
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Semestre</label>
                            <select
                                class="form-control"
                                v-model="form.id_ciclo"
                                :class="{ 'is-invalid': errors.id_ciclo }"
                            >
                                <option value="0">SELECCIONE</option>
                                <option
                                    v-for="option in ciclos"
                                    :key="option.id"
                                    :value="option.id"
                                >
                                    {{ option.descripcion }}
                                </option>
                            </select>
                            <InputError :error="errors.id_ciclo" />
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
                                v-input-upper
                                :class="{ 'is-invalid': errors.descripcion }"
                            />
                            <InputError :error="errors.descripcion" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Horas Teoria</label>
                            <input
                                class="form-control"
                                type="number"
                                v-model="form.horas_teoria"
                                :class="{ 'is-invalid': errors.horas_teoria }"
                            />
                            <InputError :error="errors.horas_teoria" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Horas Practica</label>
                            <input
                                class="form-control"
                                type="number"
                                v-model="form.horas_practica"
                                :class="{ 'is-invalid': errors.horas_practica }"
                            />
                            <InputError :error="errors.horas_practica" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Creditos Teoria</label>
                            <input
                                class="form-control"
                                type="number"
                                v-model="form.creditos_teoria"
                                :class="{
                                    'is-invalid': errors.creditos_teoria,
                                }"
                            />
                            <InputError :error="errors.creditos_teoria" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Creditos Practica</label>
                            <input
                                class="form-control"
                                type="number"
                                v-model="form.creditos_practica"
                                :class="{
                                    'is-invalid': errors.creditos_practica,
                                }"
                            />
                            <InputError :error="errors.creditos_practica" />
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <button class="btn btn-outline-danger" @click.prevent="close">
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
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import Card from "../../../Components/Card.vue";
import DataTable from "../../../Components/DataTable.vue";
import Modal from "../../../Components/Modal.vue";
import InputError from "../../../Components/InputError.vue";
import Button from "../../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../../sweetAlert2";
import SelectCompetence from "./SelectCompetence.vue";
export default {
    components: {
        SelectCompetence,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
    },
    name: "Curse",
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
                    width: "5rem",
                },
                { data: "ciclo", title: "Semestre", width: "5rem" },
                { data: "descripcion", title: "Nombre" },
                { data: "horas", title: "Horas", width: "5rem" },
                { data: "creditos", title: "Creditos", width: "5rem" },
            ],
            form: useForm({
                id: "-1",
                id_plan_estudio: 0,
                descripcion: "",
                id_ciclo: 0,
                id_modulo_competencia: 0,
                horas_teoria: "",
                horas_practica: "",
                creditos_teoria: "",
                creditos_practica: "",
            }),
            parents: [],
        };
    },
    mounted() {},
    methods: {
        onCompetencia(competencia) {
            this.form.id_modulo_competencia = competencia.id;
        },
        setForm(data) {
            this.setFormData(this.form, data);
            if (data.competencia) {
                this.$refs.competencia.setData({
                    id: data.competencia.id,
                    id_padre: data.competencia.id_padre,
                    competencia: data.competencia.competencia,
                });
            }
        },
        create() {
            this.reset();
            this.$refs.modal.show();
        },
        reset() {
            this.$refs.competencia.clean();
            this.form.reset();
            this.clearErrors('course');
        },
        close() {
            this.$refs.modal.hide();
        },
        store() {
            this.form.id_plan_estudio = this.plan.id;
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
                headers: {
                    "X-Inertia-Error-Bag": "course",
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
            return this.$page.props.errors.course || {};
        },
        plan() {
            console.log(this.$page.props.plan);
            return this.$page.props.plan || {};
        },
        ciclos() {
            return this.$page.props.ciclos || {};
        },
        urlRoute() {
            return "cursos";
        },
    },
};
</script>
