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
                                <strong>Resolver examen</strong>
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
                        <div>
                            <p class="m-0 p-0">
                                {{
                                    capitalizeWord(
                                        recurso_examen.seccion_recurso
                                            .descripcion
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                </div>
                <div class="time-progress mt-1">
                    <div class="time-remaining">
                        <strong>Tiempo restante:</strong> {{ timeRemaining }}
                    </div>
                    <div class="progress-bar">
                        <div
                            class="progress"
                            :style="{ width: progress + '%' }"
                        ></div>
                    </div>
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
                        >
                            <template #default>
                                <SimpleSelection
                                    :idPregunta="pregunta.id"
                                    :opciones="pregunta.alternativas"
                                    @update-selection="updateSelection"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo == 'OM'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #default>
                                <MultipleOption
                                    :idPregunta="pregunta.id"
                                    :opciones="pregunta.alternativas"
                                    @update-selection="updateSelection"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'VF'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #default>
                                <TrueFalseSelection
                                    :idPregunta="pregunta.id"
                                    :opciones="pregunta.alternativas"
                                    @update-selection="updateSelection"
                                />
                            </template>
                        </Option>
                        <Option
                            v-else-if="pregunta.tipo_pregunta.codigo === 'T'"
                            :index="index"
                            :descripcion="pregunta.descripcion"
                            :puntaje="pregunta.puntaje"
                        >
                            <template #default>
                                <TextAnswer
                                    :idPregunta="pregunta.id"
                                    @update-text-answer="updateTextAnswer"
                                />
                            </template>
                        </Option>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button class="btn btn-success" @click="sendAnswers">
                        Enviar
                    </button>
                </div>
            </Card>
        </div>

        <CustomModal
            :visible="showAlert"
            message="Quedan menos de 5 minutos para finalizar el examen."
            @close="closeModal"
        />
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import { capitalizeFirstWord } from "../../../utils/textProcess";
import { cleanObject } from "../../../utils/objectProcess";
import Card from "../../../Components/Card.vue";
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import MultipleOption from "../components/options/MultipleOption.vue";
import SimpleSelection from "../components/options/SimpleSelection.vue";
import TextAnswer from "../components/options/TextAnswer.vue";
import TrueFalseSelection from "../components/options/TrueFalseSelection.vue";
import Option from "../components/options/Option.vue";
import CustomModal from "../components/CustomModal.vue";
import { format, differenceInSeconds, isBefore, isAfter } from "date-fns"; // Importar funciones de date-fns
import { alertWarning } from "../../../sweetAlert2";
import Swal from "sweetalert2";

export default {
    components: {
        Card,
        LayoutContent,
        SimpleSelection,
        MultipleOption,
        TrueFalseSelection,
        TextAnswer,
        Option,
        CustomModal,
    },
    name: "SolveExam",
    props: {
        urlRoute: String,
        curso_docente: Object,
        recurso_examen: Object,
    },
    data() {
        return {
            timeRemaining: "Calculando...", // Tiempo restante en formato legible
            progress: 0, // Porcentaje de progreso
            interval: null, // Intervalo para actualizar el tiempo
            showAlert: false,
            canShowAlert: false,
            formAnswers: useForm({
                answers: [],
            }),
            fecha_actual: "",
        };
    },
    mounted() {
        this.initExam();
        this.assignAlternatives();
        this.startTimer();
    },
    beforeUnmount() {
        clearInterval(this.interval); // Limpiar el intervalo al desmontar el componente
    },
    methods: {
        redirect(go = "") {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/virtual-classroom/${go}`)
            );
        },
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        startTimer() {
            this.interval = setInterval(() => {
                const now = new Date();
                const start = new Date(this.recurso_examen.fecha_inicio);
                const end = new Date(this.recurso_examen.fecha_fin);

                if (isBefore(now, start)) {
                    this.timeRemaining = "El examen aún no ha comenzado.";
                    this.progress = 0;
                } else if (isAfter(now, end)) {
                    this.timeRemaining = "El examen ha finalizado.";
                    this.progress = 100;
                    clearInterval(this.interval);
                } else {
                    const totalDuration = differenceInSeconds(end, start);
                    const elapsed = differenceInSeconds(now, start);
                    this.progress = (elapsed / totalDuration) * 100;

                    const remainingTime = differenceInSeconds(end, now);
                    const hours = Math.floor(remainingTime / 3600);
                    const minutes = Math.floor((remainingTime % 3600) / 60);
                    const seconds = remainingTime % 60;

                    this.timeRemaining = `${hours}h ${minutes}m ${seconds}s`;
                }
            }, 1000); // Actualizar cada segundo
        },
        convertirTiempoASegundos(tiempo) {
            // Extraer horas, minutos y segundos usando una expresión regular
            const regex = /(\d+)h (\d+)m (\d+)s/;
            const match = tiempo.match(regex);

            if (!match) {
                throw new Error("Formato de tiempo no válido");
            }

            const horas = parseInt(match[1], 10); // Extraer horas
            const minutos = parseInt(match[2], 10); // Extraer minutos
            const segundos = parseInt(match[3], 10); // Extraer segundos

            // Convertir todo a segundos
            const totalSegundos = horas * 3600 + minutos * 60 + segundos;

            return totalSegundos;
        },
        closeModal() {
            this.showAlert = false; // Cerrar el modal
        },
        assignAlternatives() {
            this.formAnswers.answers = this.recurso_examen.preguntas.map(
                (pregunta) => {
                    return {
                        id_pregunta: pregunta.id,
                        id_tipo_pregunta: pregunta.id_tipo_pregunta,
                        id_answers: [],
                        text_answer: "",
                    };
                }
            );
        },
        sendAnswers() {
            const data = this.formAnswers.data();

            console.log("data", data);

            const new_data = {
                id_curso_matricula: this.curso_docente.id_curso_matricula,
                id_recurso_examen: this.recurso_examen.id,
                fecha_inicio: this.fecha_actual,
                ...data,
            };

            Swal.fire({
                title: "Cargando...",
                text: "Enviando respuestas.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/estudiantes-examenes-respuestas/store`
                ),
                data: new_data,
            })
                .then((res) => {
                    Swal.close();

                    Swal.fire({
                        icon: "success",
                        title: "Enviado",
                        text: "Examen enviado",
                        timer: 1000, // Cerrar automáticamente después de 3 segundos
                        timerProgressBar: true, // Mostrar una barra de progreso
                        showConfirmButton: false, // Ocultar el botón de confirmación
                        allowOutsideClick: false,
                    }).then(() => {
                        this.redirect(this.curso_docente.uuid);
                    });
                })
                .catch((err) => {
                    const error = err.response.data.error;
                    console.error("Error", err);
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error,
                        // timer: 3000, // Cerrar automáticamente después de 3 segundos
                        timerProgressBar: true, // Mostrar una barra de progreso
                        showConfirmButton: false, // Ocultar el botón de confirmación
                        // allowOutsideClick: false,
                    }).then(() => {
                        // this.redirect(this.curso_docente.uuid);
                    });
                });
        },

        // UPDATE ANSWERS
        updateTextAnswer({ idPregunta, textAnswer }) {
            const answerIndex = this.formAnswers.answers.findIndex(
                (answer) => answer.id_pregunta === idPregunta
            );

            if (answerIndex !== -1) {
                this.formAnswers.answers[answerIndex].text_answer = textAnswer;
            }
        },
        updateSelection(idPregunta, selectedIds) {
            const answerIndex = this.formAnswers.answers.findIndex(
                (answer) => answer.id_pregunta === idPregunta
            );

            if (answerIndex !== -1) {
                this.formAnswers.answers[answerIndex].id_answers = selectedIds;
            }
        },
        initExam() {
            // Mostrar el loading
            Swal.fire({
                title: "Cargando...",
                text: "Por favor, espera mientras se inicializa el examen.",
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                },
            });

            const data = {
                id_curso_matricula: this.curso_docente.id_curso_matricula,
                id_recurso_examen: this.recurso_examen.id,
            };

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/estudiantes-examenes/store`
                ),
                data: data,
            })
                .then((res) => {
                    console.log("response", res);
                    // Cerrar el loading
                    Swal.close();
                })
                .catch((err) => {
                    const error = err.response.data.error;
                    console.error("Error", err);
                    // Mostrar un mensaje de error con un timeout de 3 segundos
                    Swal.fire({
                        icon: "error",
                        title: "Error",
                        text: error,
                        timer: 1000, // Cerrar automáticamente después de 3 segundos
                        timerProgressBar: true, // Mostrar una barra de progreso
                        showConfirmButton: false, // Ocultar el botón de confirmación
                        allowOutsideClick: false,
                    }).then(() => {
                        this.redirect(this.curso_docente.uuid);
                    });
                });

            const fechaActual = new Date();
            this.fecha_actual = format(fechaActual, "yyyy-MM-dd HH:mm:ss");
        },
    },
    watch: {
        // Observar cambios en 'timeRemaining'
        timeRemaining(newValue) {
            const segundos = this.convertirTiempoASegundos(newValue);

            if ((segundos < 300) & !this.canShowAlert) {
                this.canShowAlert = true;
                this.showAlert = true;
            }
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

.time-progress {
    width: 100%;
}

.time-remaining {
    font-size: 14px;
    margin-bottom: 5px;
}

.progress-bar {
    width: 100%;
    height: 15px;
    background-color: #e0e0e0;
    border-radius: 5px;
    overflow: hidden;
}

.progress {
    height: 100%;
    background-color: #4caf50; /* Color de la barra de progreso */
    transition: width 1s linear;
}
</style>
