<template>
    <LayoutContent>
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <div
                        class="header-exam d-flex justify-content-center align-items-center"
                    >
                        <div>
                            <h3 class="p-0 m-0">
                                <strong>Ver respuestas</strong>
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
        </div>
    </LayoutContent>
</template>
<script>
import {
    capitalizeFirstWord,
    capitalizeFirstWordWithSpace,
} from "../../../utils/textProcess";
import Card from "../../../Components/Card.vue";
import Modal from "../../../Components/Modal.vue";
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import AddActivity from "../components/AddActivity.vue";
import MultipleOption from "../components/options/MultipleOption.vue";
import SimpleSelection from "../components/options/SimpleSelection.vue";
import TextAnswer from "../components/options/TextAnswer.vue";
import TrueFalseSelection from "../components/options/TrueFalseSelection.vue";
import InputSchedule from "../../Schedule/components/InputSchedule.vue";
import SelectSchedule from "../../Schedule/components/SelectSchedule.vue";
import AddSimpleOption from "../components/AddSimpleOption.vue";
import AddMultiOption from "../components/AddMultiOption.vue";
import AddTrueFalseSelection from "../components/AddTrueFalseSelection.vue";
import Option from "../components/options/Option.vue";
import QuestionScore from "../components/QuestionScore.vue";
import OnlineSignal from "../components/OnlineSignal.vue";
import OptionButtons from "../components/options/OptionButtons.vue";
import ExamScore from "../components/ExamScore.vue";

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
        ExamScore,
    },
    name: "Exam",
    props: {
        urlRoute: String,
        curso_docente: Object,
        recurso_examen: Object,
        info_estudiante: Object,
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        capitalizeFullWord(text) {
            return capitalizeFirstWordWithSpace(text);
        },
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
