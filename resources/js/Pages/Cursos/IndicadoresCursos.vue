<template>
    <LayoutContent>
        <section class="indicadores-cursos">
            <div class="row mb-1">
                <div class="col-md-6 col-lg-6">
                    <Button
                        ref="btnEdit"
                        class="btn btn-secondary"
                        @click.native.prevent="remember"
                    >
                        <feather-icon icon="ArrowLeftIcon" class="mr-50" />
                        <span class="align-small">Volver</span>
                    </Button>
                </div>
                <div
                    class="col-md-6 col-lg-6"
                    style="display: flex; justify-content: end"
                >
                    <button
                        title="Agregar nuevo indicador"
                        class="btn btn-primary"
                        @click.prevent="create"
                        v-if="estadoCurso != 'S'"
                    >
                        <feather-icon icon="PlusIcon" class="mr-50" />
                        <span class="align-middle">Nuevo</span>
                    </button>
                </div>
            </div>
            <div>
                <div class="row">
                    <div class="col-md-12 col-lg-12 mb-2">
                        <TableAcordion
                            :estado="estadoCurso"
                            :indicadores="indicadoresView"
                            :edit="edit"
                            :destroy="destroy"
                            :semanas="semanas"
                            :tipoEvaluacion="tipo_evaluacion"
                            :urlRouteCriterios="urlRouteCriterios"
                            :uuid="uuid"
                        />
                    </div>
                    <div class="not-length" v-if="indicadoresView.length == 0">
                        <feather-icon icon="InboxIcon" />
                        <span class="align-small"
                            >No se registraron indicadores</span
                        >
                    </div>

                    <!-- <div
                            class="col-md-12 col-lg-12 mb-0"
                            v-for="(item, index) in indicadores"
                            :key="item.id"
                        >
                            <BCard
                                border-variant="primary"
                                header-bg-variant="primary"
                                header-text-variant="white"
                                align="center"
                            >
                                <template #header>
                                    <h5
                                        class="bg-primary text-white header-indicador"
                                        style="
                                            display: flex;
                                            justify-content: center;
                                        "
                                    >
                                        <feather-icon
                                            icon="ArchiveIcon"
                                            class="mr-50"
                                        />
                                        {{ `Indicador ${index + 1}` }}
                                    </h5>
                                    <div>
                                        <Button
                                            ref="btnEdit"
                                            class="btn btn-success btn-sm"
                                            @click.native.prevent="
                                                createEvalucion(item.id)
                                            "
                                        >
                                            <feather-icon
                                                icon="PlusIcon"
                                                class="mr-20"
                                            />
                                            <span class="align-middle"
                                                >Evaluaciones</span
                                            >
                                        </Button>
                                        ||
                                        <Button
                                            ref="btnEdit"
                                            class="btn btn-warning btn-sm"
                                            @click.native.prevent="edit"
                                        >
                                            <feather-icon
                                                icon="EditIcon"
                                                class="mr-20"
                                            />
                                        </Button>
                                        <Button
                                            ref="btnDestroy"
                                            class="btn btn-danger btn-sm"
                                            @click.native.prevent="destroy"
                                        >
                                            <feather-icon
                                                icon="Trash2Icon"
                                                class="mr-20"
                                            />
                                        </Button>
                                    </div>
                                </template>
                                <BCardText>
                                    <p class="indicador-text">
                                        {{ item.descripcion }}
                                    </p>
                                    <p>
                                        {{
                                            `${item.semana_inicio} - ${item.semana_fin}`
                                        }}
                                    </p>
                                </BCardText>
                            </BCard>
                        </div> -->
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <!--ESPACIO PARA EL SPIN  -->
                    <div class="spinner" v-if="loadingCursos">
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>
                </div>
            </div>
            <Modal ref="modalI" :loading="form.processing">
                <template #title>Indicador</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Código
                                    <span class="required"> * </span></label
                                >
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="form.codigo"
                                    :class="{
                                        'is-invalid': errors.codigo,
                                    }"
                                    v-input-upper
                                />
                                <InputError :error="errors.codigo" />
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
                                    >Semanda de Inicio
                                    <span class="required"> * </span></label
                                >
                                <select
                                    @change="validateWeek"
                                    class="form-control"
                                    v-model="form.semana_inicio"
                                    :class="{
                                        'is-invalid': errors.id_tipo_matricula,
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
                                <InputError :error="errors.semana_inicio" />
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label
                                    >Semana de Fin
                                    <span class="required"> * </span></label
                                >
                                <select
                                    @change="validateWeek"
                                    class="form-control"
                                    v-model="form.semana_fin"
                                    :class="{
                                        'is-invalid': errors.id_tipo_matricula,
                                    }"
                                >
                                    <option
                                        v-for="(option, index) in semanas"
                                        :key="option.id"
                                        :value="option.id"
                                    >
                                        {{ option.nombre }}
                                    </option>
                                </select>
                                <InputError :error="errors.semana_fin" />
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
import CardCourse from "../../Components/CardCourse.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
import {
    BCard,
    BCardText,
    BFormInput,
    BInputGroup,
    BSpinner,
} from "bootstrap-vue";
import TableStudent from "../../Components/TableStudent.vue";
import TableAcordion from "../../Components/TableAcordion.vue";
import TableCriterios from "./components/TableCriterios.vue";
export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        BSpinner,
        CardCourse,
        TableStudent,
        TableCriterios,
        BCard,
        BCardText,
        Modal,
        BFormInput,
        BInputGroup,
        TableAcordion,
    },
    name: "Module",
    props: {
        curso: Object,
        tipo_evaluacion: Object,
        urlRoute: String,
        institutos: Array,
        secciones: Array,
        turnos: Array,
        ciclos: Array,
        periodo: Array,
        estudiantes: Array,
        semanas: Array,
        cursoDocente: Object,
        indicadores: Array,
        estado: Object,
        uuid: String,
        urlRouteCriterios: String,
    },
    data() {
        return {
            loadingCursos: false,
            indicadoresView: [...this.indicadores],
            estadoCurso: "",
            //Table Evaluación
            evaluacionxIndicador: [],
            form: useForm({
                //¿?
                id: "-1",
                codigo: "",
                porcentaje: "",
                descripcion: "",
                semana_inicio: "",
                semana_fin: "",
                id_curso_docente: "",
                uuid: "",
            }),

            parents: [],
        };
    },
    // created() {
    //     console.log(this.estudiantes);
    //     this.id_periodo = this.periodo[0].id;
    // },
    mounted() {
        console.log("Curso", this.cursoDocente);
        this.estadoCurso = this.estado.culminado;
    },
    methods: {
        //validación de porcentajes
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
        //Validación de las semanas de inicio y fin
        validateWeek() {
            console.log(this.form.semana_inicio);
            if (this.form.semana_inicio >= this.form.semana_fin) {
                if (this.form.semana_fin && this.form.semana_inicio) {
                    alertWarning(
                        "La semana de inicio debe ser mayor que la semana de fin"
                    );
                    this.form.semana_fin = "";
                }
            }
        },
        //Regresar
        remember() {
            this.$inertia.visit(this.routeTo(`gestion-cursos/${this.uuid}`));
        },
        //Llámada a indicadores
        getIndicadores() {
            this.$http
                .get(this.routeTo(`${this.urlRoute}${this.uuid}`))
                .then((res) => {
                    this.indicadoresView = res.data;
                    console.log(this.indicadoresView);
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    console.log("ok");
                });
        },

        setForm(data) {
            this.setFormData(this.form, data);
        },

        create() {
            this.form.reset();
            this.$refs.modalI.show();
        },

        close() {
            this.$refs.modalI.hide();
        },
        ordenarIndicadores(evaluaciones) {
            const agrupado = evaluaciones.reduce((acc, curr) => {
                const key = curr.id_indicador;
                if (!acc[key]) {
                    acc[key] = { indicador: curr.indicador, criterios: [] };
                }
                acc[key].criterios.push({
                    nombre: curr.nombre,
                    semana: curr.semana,
                    porcentaje: curr.porcentaje,
                });
                return acc;
            }, {});
            this.evaluacionxIndicador = Object.values(agrupado);
            console.log(this.evaluacionxIndicador);
        },

        store() {
            this.form.processing = true;
            this.form.id_curso_docente = this.cursoDocente.id;
            this.form.uuid = this.uuid;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    //Validación del response de la llamada
                    if (res.data.data == false) {
                        alertWarning(res.data.error);
                    } else {
                        alertSuccess("Indicador Guardado Correctamente");
                        this.$refs.modalI.hide();
                        this.addIndicadores(res.data);
                        this.getIndicadores();
                    }
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
        },
        addIndicadores(data) {
            this.indicadoresView.push({ ...data });
            console.log("Indicadores", this.indicadoresView);
        },
        edit(id) {
            this.form.reset();

            this.$http
                .get(this.routeTo(`${this.urlRoute}${id}/edit`))
                .then((res) => {
                    res.data.porcentaje = parseInt(res.data.porcentaje);
                    this.setForm(res.data);
                    this.$refs.modalI.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {});
        },
        destroy(id) {
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    this.$http
                        .delete(this.routeTo(`${this.urlRoute}${id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.getIndicadores();
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
.required {
    color: red;
    margin-left: 2px;
}
.not-length {
    height: 50px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.not-length .feather {
    height: 4rem !important;
}
.card-indicadores {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px;
}
.header-indicador {
    .feather {
        height: 2.5vh;
    }
}
.indicador-text {
    margin-top: 10px;
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 17px;
    font-weight: bold;
}
.imgContent {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100px;
    width: 100%;
    .feather {
        color: black;
        height: 10vh;
        margin-bottom: 8px;
    }
}
.spinner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
}
.card-title {
    display: -webkit-box; /* Usa un contenedor de caja flexible */
    -webkit-line-clamp: 3; /* Limita el texto a 3 líneas */
    -webkit-box-orient: vertical; /* Define la dirección de las líneas (vertical) */
    overflow: hidden; /* Oculta cualquier contenido que se desborde */
    text-overflow: ellipsis;
}
.cursos {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}

.state {
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    width: 4vw;
    background-color: rgb(5, 207, 5);
    font-size: 12px;
    color: white;
}
.state :hover {
    background-color: rgb(5, 207, 5);
}
.card-bodys {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.info-curso {
    display: flex;
    align-items: center;
    p {
        margin-bottom: 0;
    }
    .feather {
        height: 3.5vh;
        margin-bottom: 8px;
    }
}
.cursos .card {
    border: 1px solid black !important;
}
.informacion {
    display: flex;
    flex-direction: column;
    margin-bottom: 10px;
}

.filter-title {
    display: flex;
    align-items: center;
    width: 100%;
    justify-content: space-between;
}
.datos {
    display: flex;
    gap: 30px;
}
.datos-curso {
    display: flex;
}
.datos-header {
    display: flex;
    width: 100%;
    justify-content: space-between;
    align-items: center;
}
.datos-button {
    display: flex;
    justify-content: end;
    align-items: center;
}
</style>
