<template>
    <div class="accordion" role="tablist">
        <BCard
            no-body
            class="mb-1"
            v-for="(item, index) in indicadoresView"
            :key="item.id"
        >
            <BCardHeader
                @click="getCriterioEvaluacion(item, index)"
                header-tag="header"
                class="p-1"
                role="tab"
            >
                <div
                    v-b-toggle="`accordion-${index}`"
                    class="indicador"
                    variant="outline-ligth"
                >
                    <p style="display: flex; height: 6vh; align-items: center">
                        {{ ` INDICADOR ${index + 1} - ${item.descripcion} ` }}
                    </p>

                    <section class="buttons-indicador">
                        <BBadge
                            variant="light-success"
                            style="margin-left: 10px; height: 20px"
                        >
                            {{ `PORCENTAJE ${parseInt(item.porcentaje)}%  ` }}
                        </BBadge>
                        <div
                            v-if="item.culminado != 'S'"
                            @click.stop="getCriterioEvaluacion(item)"
                        >
                            <Button
                                title="Añadir Evaluación"
                                ref="btnSucces"
                                class="btn btn-success btn-sm"
                                @click.native.prevent="create(item.id)"
                            >
                                <feather-icon icon="PlusIcon" class="mr-20" />
                                <span class="align-small">Evaluación</span>
                            </Button>
                            ||
                            <Button
                                title="Editar"
                                ref="btnEdit"
                                class="indicador-btn btn btn-warning btn-sm btn-icon"
                                @click.native.prevent="editA(item.id, index)"
                            >
                                <feather-icon icon="EditIcon" class="mr-20" />
                            </Button>
                            <Button
                                title="Eliminar"
                                ref="btnDestroy"
                                class="indicador-btn btn btn-danger btn-sm btn-icon"
                                @click.native.prevent="destroyA(item.id, index)"
                            >
                                <feather-icon icon="Trash2Icon" class="mr-20" />
                            </Button>
                        </div>
                        <feather-icon
                            v-if="item.arrow == true"
                            icon="ChevronUpIcon"
                            style="height: 1.5rem"
                        />
                        <feather-icon
                            v-else
                            icon="ChevronDownIcon"
                            style="height: 1.5rem"
                        />
                    </section>
                </div>
            </BCardHeader>
            <BCollapse
                :id="`accordion-${index}`"
                accordion="my-accordion"
                role="tabpanel"
            >
                <BCardBody @click.stop>
                    <div class="spinner" v-if="loadingEvaluación">
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>

                    <TableCriterios
                        v-else
                        :evaluaciones="item.evaluaciones"
                        :urlRouteCriterios="urlRouteCriterios"
                        :semanas="semanas"
                        :estadoCurso="estadoCurso"
                        :uuid="uuid"
                        @editE="editE"
                    />
                    <div
                        style="
                            display: flex;
                            justify-content: end;
                            padding-top: 20px;
                        "
                    >
                        <p>
                            <strong>
                                {{
                                    `${searchSemana(
                                        semanas,
                                        item.semana_inicio
                                    )}  -  ${searchSemana(
                                        semanas,
                                        item.semana_fin
                                    )}`
                                }}.
                            </strong>
                        </p>
                    </div>
                </BCardBody>
            </BCollapse>
        </BCard>
        <Modal ref="modalEvaluacion" :loading="form.processing">
            <template #title>Evaluación</template>
            <form class="form form-vertical">
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label
                                >Nombre <span class="required"> * </span></label
                            >
                            <input
                                class="form-control"
                                type="text"
                                v-model="form.nombre"
                                :class="{
                                    'is-invalid': errors.nombre,
                                }"
                                v-input-upper
                            />
                            <InputError :error="errors.nombre" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label
                                >Porcentaje
                                <span class="required"> * </span></label
                            >
                            <BInputGroup append="%">
                                <BFormInput
                                    @input="validatePocentaje"
                                    class="form-control"
                                    type="number"
                                    v-model="form.porcentaje"
                                >
                                </BFormInput>
                            </BInputGroup>

                            <InputError :error="errors.porcentaje" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label
                                >Descripción
                                <span class="required"> * </span></label
                            >
                            <textarea
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
                            <label
                                >Semana <span class="required"> * </span></label
                            >
                            <select
                                class="form-control"
                                v-model="form.semana"
                                :class="{
                                    'is-invalid': errors.semana,
                                }"
                            >
                                <option
                                    v-for="option in semanas"
                                    :key="option.id"
                                    :value="option.id"
                                >
                                    {{ option.nombre }}
                                </option>
                            </select>
                            <InputError :error="errors.id_tipo_matricula" />
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label
                                >Tipo de Evalución
                                <span class="required"> * </span></label
                            >
                            <select
                                class="form-control"
                                v-model="form.tipo_evaluacion"
                                :class="{
                                    'is-invalid': errors.tipo_evaluacion,
                                }"
                            >
                                <option :value="tipoEvaluacion.CRITERIO">
                                    {{ tipoEvaluacion.CRITERIO }}
                                </option>
                                <option :value="tipoEvaluacion.SUSTITUTORIO">
                                    {{ tipoEvaluacion.SUSTITUTORIO }}
                                </option>
                            </select>
                            <InputError :error="errors.tipo_evaluacion" />
                        </div>
                    </div>
                </div>
            </form>
            <template #footer>
                <button class="btn btn-outline-danger" @click.prevent="close()">
                    <feather-icon icon="XIcon" />
                    <span>Cancelar</span>
                </button>
                <button
                    class="btn btn-success"
                    @click.prevent="store()"
                    :disabled="form.processing"
                >
                    <feather-icon icon="SaveIcon" />
                    <span>Guardar</span>
                </button>
            </template>
        </Modal>
    </div>
</template>

<script>
import {
    BButton,
    BCard,
    BCardBody,
    BCardHeader,
    BCardText,
    BCollapse,
    BBadge,
    BSpinner,
} from "bootstrap-vue";
import Button from "./Button.vue";
import Modal from "./Modal.vue";
import { useForm } from "@inertiajs/vue2";
import { alertSuccess, alertWarning, confirm } from "../sweetAlert2";
import TableCriterios from "../Pages/Cursos/components/TableCriterios.vue";
import InputError from "./InputError.vue";
import { searchSemana } from "../helpers";

export default {
    name: "TablaAcordion",
    props: {
        indicadores: Array,
        edit: Function,
        destroy: Function,
        semanas: Array,
        tipoEvaluacion: Object,
        urlRouteCriterios: String,
        uuid: String,
        estado: String,
    },
    components: {
        BButton,
        BCard,
        BSpinner,
        BCardBody,
        BCardHeader,
        BCardText,
        BCollapse,
        Button,
        BBadge,
        Modal,
        InputError,
        TableCriterios,
    },
    data() {
        return {
            estadoGet: false,
            evaluaciones: [],
            loadingEvaluación: false,
            keyIndicador: "",
            estadoCurso: "",
            indicadoresView: [...this.indicadores],
            arrowCont: 0,
            form: useForm({
                //¿?
                id: "-1",
                nombre: "",
                porcentaje: "",
                semana: "",
                tipo_evaluacion: "",
                id_curso_docente_indicador: "",
                descripcion: "",
                id_indicador: "",
            }),
        };
    },
    mounted() {
        this.estadoCurso = this.estado;
        this.indicadores.forEach((item) => {
            item.arrow = false;
        });
    },
    methods: {
        searchSemana,
        validatePocentaje() {
            const porcentaje = parseFloat(this.form.porcentaje);
            if (!isNaN(porcentaje)) {
                if (porcentaje <= "0") {
                    alertWarning(
                        "El porcentaje no debe ser menor o igual a 0%"
                    );
                    this.form.porcentaje = " ";
                    this.$forceUpdate();
                } else if (porcentaje > 100) {
                    alertWarning("El porcentaje no debe exceder el 100%");
                    this.form.porcentaje = "";
                }
            }
        },
        getCriterioEvaluacion(item, indexA) {
            this.indicadores.forEach((item, index) => {
                if (indexA == index) {
                    item.arrow = true;
                } else {
                    item.arrow = false;
                }
            });
            if (item.evaluaciones) {
                return;
            }
            this.loadingEvaluación = true;
            item.evaluaciones = [];
            this.$http
                .get(this.routeTo(`${this.urlRouteCriterios}${item.id}`))
                .then((res) => {
                    item.evaluaciones = res.data;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.loadingEvaluación = false;
                    this.estadoGet = true;
                });
        },
        IconStyles(id) {
            console.log("object");
        },
        async editA(id, index) {
            this.$refs.btnEdit[index].load();
            await this.edit(id);
            this.$refs.btnEdit[index].reset();
        },
        async destroyA(id, index) {
            this.$refs.btnDestroy[index].load();
            this.destroy(id);
            this.$refs.btnDestroy[index].reset();
        },
        create(id) {
            this.form.reset();
            this.keyIndicador = id;
            this.$refs.modalEvaluacion.show();
            this.form.id_curso_docente_indicador = id;
        },
        store() {
            this.form.processing = true;
            let fn;
            if (this.form.id == -1) {
                fn = this.addEvaluacion;
            } else {
                fn = this.editEvaluaciones;
            }
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRouteCriterios}`),
                data: this.form.data(),
            })
                .then((res) => {
                    if (res.data.data == false) {
                        alertWarning(res.data.error);
                    } else {
                        alertSuccess("Evaluación Guardada Correctamente");
                        this.$refs.modalEvaluacion.hide();
                        fn(res.data);
                    }
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
        },
        addEvaluacion(data) {
            data.estado_calificacion = "PENDIENTE";
            const index = this.indicadoresView.findIndex((el) => {
                return el.id == data.id_curso_docente_indicador;
            });
            //Forzamos la reactividad
            if (index !== -1) {
                this.indicadoresView[index].evaluaciones.push(data);
            }
            console.log(this.indicadoresView[index].evaluaciones);
            //Forzamos la reactividad x2
            this.indicadoresView = [...this.indicadoresView];
        },
        //Función para rellenar el formulario
        setForm(data) {
            this.setFormData(this.form, data);
        },
        close() {
            this.$refs.modalEvaluacion.hide();
        },
        editE(id) {
            this.form.reset();
            this.$http
                .get(this.routeTo(`${this.urlRouteCriterios}${id}/edit`))
                .then((res) => {
                    this.form.id_indicador =
                        res.data.id_curso_docente_indicador;
                    res.data.porcentaje = parseInt(res.data.porcentaje);
                    this.setForm(res.data);
                    this.$refs.modalEvaluacion.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    console.log("ok");
                });
        },
        editEvaluaciones(data) {
            const index = this.indicadoresView.findIndex(
                (item) => item.id == data.id_curso_docente_indicador
            );
            const evaluacion = this.indicadoresView[
                index
            ].evaluaciones.findIndex((el) => el.id == data.id);
            this.$set(
                this.indicadoresView[index].evaluaciones,
                evaluacion,
                data
            );
            console.log(this.indicadoresView[index].evaluaciones[evaluacion]);
            this.indicadoresView = [...this.indicadoresView];
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors || {};
        },
    },
    //Sirve para escuchar cambios del padre al hijo, de esta manera se la variable se vuelve reactiva
    watch: {
        indicadores(newVal) {
            this.indicadoresView = [...newVal];
        },
    },
};
</script>
<style scoped>
.required {
    color: red;
    margin-left: 2px;
}
.card .card-header {
    display: block !important;
}

.btn {
    width: auto !important;
}
.indicador {
    display: grid;
    align-items: center;
    grid-template-columns: 11fr 6fr;
    margin-left: 20px;
    margin-right: 20px;
    height: 10vh;
    p {
        margin-top: 0;
        margin-bottom: 0 !important;
    }
}
.mb-1,
.my-1 {
    margin-bottom: 0 !important;
}
.card {
}
.card:hover {
    font-size: 15px;
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.14) !important;
}
.p-1 {
    padding: 0rem !important;
}

.spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.card-body {
    padding-top: 0rem !important;
}

.buttons-indicador {
    display: flex;
    gap: 10px;
    justify-content: end;
    align-items: center;
}

@media (max-width: 1135px) {
    .indicador {
        display: grid;
        align-items: center;
        grid-template-columns: none;
        grid-template-rows: 1fr 1fr;
    }
    .buttons-indicador {
        justify-content: start;
        margin-left: 0;
        margin-bottom: 20px;
    }
}
</style>
