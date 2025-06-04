<template>
    <LayoutContent>
        <ReturnButton
            title="Volver"
            :path_remove="`${info_estudiante.id_curso_matricula}`"
        />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <div
                        class="header-exam d-flex justify-content-center align-items-center"
                    >
                        <div>
                            <h3 class="p-0 m-0">
                                <strong>Calificar manualmente</strong>
                            </h3>
                        </div>
                    </div>
                </template>
                <div class="header-options">
                    <div>
                        <h5>
                            <strong>Examen: </strong>
                            {{
                                capitalizeWord(
                                    recurso_examen.seccion_recurso.nombre
                                )
                            }}
                        </h5>
                        <h5>
                            <strong>Estudiante: </strong>
                            {{
                                capitalizeFullWord(
                                    `${info_estudiante.numero_documento} - ${info_estudiante.nombres} ${info_estudiante.apellido_paterno} ${info_estudiante.apellido_materno}`
                                )
                            }}
                        </h5>
                    </div>
                    <ExamScore
                        :puntaje_examen="recurso_examen.puntaje_examen"
                        :estado_examen="recurso_examen.estado_examen"
                    />
                </div>
                <div class="pt-1">
                    <p><strong>Preguntas:</strong></p>
                    <div
                        v-for="(pregunta, index) in recurso_examen.preguntas"
                        :key="pregunta.id"
                    >
                        <Option
                            v-if="pregunta.tipo_pregunta.codigo == 'SM'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                            :respuestas="pregunta"
                        >
                            <template #default>
                                <SimpleSelection
                                    :opciones="pregunta.alternativas"
                                    :respuestas="pregunta.respuestas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo == 'OM'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                            :respuestas="pregunta"
                        >
                            <template #default>
                                <MultipleOption
                                    :opciones="pregunta.alternativas"
                                    :respuestas="pregunta.respuestas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'VF'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                            :respuestas="pregunta"
                        >
                            <template #default>
                                <TrueFalseSelection
                                    :opciones="pregunta.alternativas"
                                    :respuestas="pregunta.respuestas"
                                    :disabled="true"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'T'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                            :respuestas="pregunta"
                        >
                            <template #options-btn>
                                <OptionButtonsManualQualificate
                                    @edit="createCalificar(pregunta, index)"
                                />
                            </template>
                            <template #default>
                                <TextAnswer
                                    :disabled="true"
                                    :respuestas="pregunta.respuestas"
                                />
                            </template>
                        </Option>
                    </div>
                </div>
            </Card>

            <Modal ref="modalCalificar" :loading="formPregunta.processing">
                <template #title>Calificar respuesta</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="container-score__pregunta">
                                <h5 class="m-0 p-0">
                                    Pregunta {{ formPregunta.index }}:
                                </h5>
                                <p>{{ formPregunta.descripcion }}</p>
                            </div>
                            <div class="container-score__pregunta">
                                <h5 class="m-0 p-0">Respuesta:</h5>
                                <p class="m-0 p-0">
                                    {{ formPregunta.respuesta }}
                                </p>
                            </div>
                            <hr />
                            <InputSchedule
                                label="Puntaje"
                                :modelValue="formPregunta.puntaje"
                                @click-input="
                                    () => resetInput(formPregunta, 'puntaje')
                                "
                                :hasError="v$.formPregunta.puntaje.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'puntaje',
                                            formPregunta
                                        )
                                "
                                :errorMessage="
                                    v$.formPregunta.puntaje.$error
                                        ? 'El valor no debe contener comas.'
                                        : ''
                                "
                                :disabled="asignar_todo"
                            />

                            <div class="footer-add-score">
                                <p class="puntos-disponibles m-0 p-0">
                                    Puntos disponibles:
                                    <span>{{ score_range.toFixed(2) }}</span>
                                </p>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="asignar_todo"
                                        v-model="asignar_todo"
                                        @change="asignarTodo()"
                                    />
                                    <label
                                        class="form-check-label"
                                        for="asignar_todo"
                                    >
                                        Asignar todo
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeCalificar"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cerrar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeCalificar"
                        :disabled="formPregunta.processing"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Calificar</span>
                    </button>
                </template>
            </Modal>
        </div>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import {
    capitalizeFirstWord,
    capitalizeFirstWordWithSpace,
} from "../../utils/textProcess";
import { alertError, alertSuccess } from "../../sweetAlert2";
import useVuelidate from "@vuelidate/core";
import Card from "../../Components/Card.vue";
import Modal from "../../Components/Modal.vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import AddActivity from "./components/AddActivity.vue";
import MultipleOption from "./components/options/MultipleOption.vue";
import SimpleSelection from "./components/options/SimpleSelection.vue";
import TextAnswer from "./components/options/TextAnswer.vue";
import TrueFalseSelection from "./components/options/TrueFalseSelection.vue";
import InputSchedule from "../Schedule/components/InputSchedule.vue";
import SelectSchedule from "../Schedule/components/SelectSchedule.vue";
import AddSimpleOption from "./components/AddSimpleOption.vue";
import AddMultiOption from "./components/AddMultiOption.vue";
import AddTrueFalseSelection from "./components/AddTrueFalseSelection.vue";
import Option from "./components/options/Option.vue";
import QuestionScore from "./components/QuestionScore.vue";
import OnlineSignal from "./components/OnlineSignal.vue";
import OptionButtons from "./components/options/OptionButtons.vue";
import OptionButtonsManualQualificate from "./components/options/OptionButtonsManualQualificate.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import ExamScore from "./components/ExamScore.vue";
import { qualificationsValidator2 } from "../../Validators/qualificationsValidator2";

export default {
    components: {
        Card,
        LayoutContent,
        SimpleSelection,
        MultipleOption,
        TrueFalseSelection,
        TextAnswer,
        AddActivity,
        Modal,
        InputSchedule,
        SelectSchedule,
        AddSimpleOption,
        AddMultiOption,
        AddTrueFalseSelection,
        Option,
        QuestionScore,
        OnlineSignal,
        OptionButtons,
        OptionButtonsManualQualificate,
        ReturnButton,
        ExamScore,
    },
    name: "ManualQualification",
    props: {
        urlRoute: String,
        curso_docente: Object,
        recurso_examen: Object,
        info_estudiante: Object,
    },
    data() {
        return {
            formPregunta: useForm({
                id_pregunta: 0,
                id_respuesta: 0,
                index: 0,
                descripcion: "",
                respuesta: "",
                puntaje: "",
            }),

            asignar_todo: false,
            score_range: 0,
        };
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        capitalizeFullWord(text) {
            return capitalizeFirstWordWithSpace(text);
        },
        updateField(value, field, form) {
            this.$set(form, field, value);
        },

        resetInput(form, field) {
            form[field] = "";
            this.total_plus = "";
            this.total_subtract = "";
        },

        asignarTodo() {
            if (this.asignar_todo) {
                this.formPregunta.puntaje = this.score_range.toFixed(2);
            } else {
                this.formPregunta.puntaje = "";
            }
        },

        // MODAL

        createCalificar(value, index) {
            this.formPregunta.reset();

            this.formPregunta.id_pregunta = value.id;
            this.formPregunta.id_respuesta = value.respuestas[0].id;
            this.formPregunta.descripcion = this.capitalizeWord(
                value.descripcion
            );
            this.formPregunta.respuesta = value.respuestas[0].respuesta;
            this.formPregunta.index = index + 1;

            const score = parseFloat(value.puntaje);
            this.score_range = score;

            console.log(this.formPregunta.data());

            this.$refs.modalCalificar.show();
        },
        closeCalificar() {
            this.$refs.modalCalificar.hide();
        },
        storeCalificar() {
            this.v$.formPregunta.puntaje.$touch();
            if (this.v$.formPregunta.puntaje.$invalid) {
                return;
            }

            const data = this.formPregunta.data();

            const new_data = {
                id_pregunta: data.id_pregunta,
                id_respuesta: data.id_respuesta,
                id_estudiante_examen: this.recurso_examen.id_estudiante_examen,
                puntaje: data.puntaje,
            };

            console.log("new_data", new_data);

            this.formPregunta.processing = true;

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/virtual-classroom/estudiantes-examenes-respuestas/update-score-text`
                ),
                data: new_data,
            })
                .then((res) => {
                    alertSuccess("Respuesta calificada correctamente");
                    this.$inertia.reload({
                        only: ["recurso_examen"],
                    });

                    this.formPregunta.reset();
                    this.asignar_todo = false;
                    this.closeCalificar();
                })
                .catch((err) => {
                    alertError(err.response.data.error);
                })
                .finally(() => {
                    this.formPregunta.processing = false;
                });
        },
    },
    setup() {
        const v$ = useVuelidate();

        return {
            v$,
        };
    },
    validations() {
        return {
            formPregunta: {
                puntaje: {
                    qualificationsValidator: qualificationsValidator2(20),
                },
            },
        };
    },
};
</script>

<style scoped>
.header-exam {
    gap: 8px;

    .signal {
        padding-top: 2.8px;
    }
}
.header-options {
    display: flex;
    justify-content: space-between;
}

.btn-control {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    p {
        font-size: 12px;
    }

    button {
        position: relative;

        .alert-icon {
            position: absolute;
            top: -10px;
            left: -10px;

            border: 1px solid #b71c1c; /* Rojo oscuro para el borde */
            border-radius: 100%;
            background-color: #d32f2f; /* Rojo intenso */

            padding: 6px;

            display: flex;
            justify-content: center;
            align-items: center;

            animation: blink 2s infinite alternate; /* Alterna cada 1s */
        }
    }
}

.container-score__pregunta {
    display: flex;
    flex-direction: column;
    flex: 1;
}

.container-score__pregunta p {
    font-size: 13px;
}

.puntos-disponibles {
    font-size: 12px;
    span {
        font-weight: bold;
    }
}

@keyframes blink {
    0% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
    50% {
        background-color: #f44336; /* Rojo más brillante (fondo) */
        border-color: #d32f2f; /* Rojo intenso (borde) */
        color: white; /* Texto blanco cuando el fondo se hace más brillante */
    }
    100% {
        background-color: #ffebeb; /* Rojo muy claro (fondo) */
        border-color: #b71c1c; /* Rojo muy intenso (borde) */
        color: #b71c1c; /* Rojo intenso para el texto */
    }
}

.form {
    display: flex;
    flex-direction: column;
}

.question-score-wrapper:not(:last-child) {
    border-bottom: 1px solid #ddd;
    margin-bottom: 8px; /* Espaciado opcional */
}

.calcular-puntajes-container {
    display: flex;
    gap: 6px;
    border-bottom: 1px solid #ddd;
    margin-bottom: 8px;

    button {
        border-top-right-radius: 0px !important;
        border-bottom-right-radius: 0px !important;
    }

    .calcular-btn {
        display: flex;
        align-items: center;
        cursor: pointer;

        .title {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #82868b;
            color: white;
            height: 100%;
            min-width: 80px;
            padding: 0 10px;
            border-left: 1px solid #ffffff;

            border-top-right-radius: 0.358rem;
            border-bottom-right-radius: 0.358rem;

            p {
                font-size: 12px;
            }
        }

        .title-dark {
            background-color: #4b4b4b;
        }
    }
}

.footer-add-score {
    display: flex;
    justify-content: space-between;
}

.title-alternativas {
    font-size: 16px;
}

.header-alternativas {
    gap: 20px;

    p {
        text-align: justify;
    }

    .btn-header-alternativa {
        min-width: 140px;
    }
}

.header-text-alternativa {
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
}
</style>
