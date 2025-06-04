<template>
    <LayoutContent>
        <ReturnButton
            title="Volver"
            :path_remove="`foros/${recurso_foro.id}/calificar`"
        />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <h3><strong>Calificar Foro</strong></h3>
                </template>
                <div class="container-filtro pb-1 row">
                    <div class="col-sm-8">
                        <h5>
                            <strong>Foro: </strong>
                            {{
                                capitalizeWord(
                                    recurso_foro.seccion_recurso.nombre
                                )
                            }}
                        </h5>
                        <div>
                            <p class="m-0 p-0">
                                {{
                                    capitalizeWord(
                                        recurso_foro.seccion_recurso.descripcion
                                    )
                                }}
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-4 m-0 filtro">
                        <input
                            v-model="searchTextSend"
                            type="text"
                            class="form-control"
                            placeholder="Buscar por dni, nombre, apellido paterno o materno"
                        />
                    </div>
                </div>
                <div class="contenidos row">
                    <div
                        v-for="estudiante in filteredEstudiantesSend(
                            respuestas_send
                        )"
                        :key="estudiante.id"
                        class="acordeon-container col-md-12"
                        :id="'estudiante-' + estudiante.id"
                    >
                        <div class="acordeon">
                            <div
                                class="acordeon-header d-flex justify-content-between align-items-center"
                            >
                                <div
                                    class="acordeon-header__title d-flex justify-content-start"
                                    @click="toggleEstudiante(estudiante.id)"
                                    style="cursor: pointer"
                                >
                                    <span>
                                        <feather-icon
                                            stroke-width="3px"
                                            size="12"
                                            :icon="
                                                isVisible(estudiante.id)
                                                    ? 'ArrowDownIcon'
                                                    : 'ArrowRightIcon'
                                            "
                                        ></feather-icon>
                                    </span>
                                    <h4 class="m-0 p-0">
                                        {{ estudiante.numero_documento }} -
                                        {{
                                            capitalizeWordSpace(
                                                estudiante.nombres
                                            )
                                        }}
                                        {{
                                            capitalizeWord(
                                                estudiante.apellido_paterno
                                            )
                                        }}
                                        {{
                                            capitalizeWord(
                                                estudiante.apellido_materno
                                            )
                                        }}
                                    </h4>
                                </div>
                                <button
                                    v-if="
                                        !visibleEstudiante.includes(
                                            estudiante.id
                                        )
                                    "
                                    @click="
                                        createCalificacion(
                                            estudiante.respuestas[
                                                estudiante.respuestas.length - 1
                                            ].id
                                        )
                                    "
                                    class="btn-calificacion btn btn-primary"
                                >
                                    {{ getCalificacion(estudiante) }}
                                </button>
                            </div>
                            <transition
                                name="slide"
                                @before-enter="beforeEnter"
                                @enter="enter"
                                @leave="leave"
                            >
                                <div
                                    v-show="
                                        isVisible(
                                            estudiante.id,
                                            estudiante.nombres
                                        )
                                    "
                                    class="acordeon-body"
                                >
                                    <div
                                        v-for="(
                                            respuesta, index
                                        ) in estudiante.respuestas"
                                        :key="respuesta.id"
                                        class="archivo-container"
                                    >
                                        <Commentary
                                            :comentario="respuesta.descripcion"
                                            :secondary="
                                                index !=
                                                estudiante.respuestas.length - 1
                                            "
                                            :primary="
                                                index ==
                                                estudiante.respuestas.length - 1
                                            "
                                            style="width: 100%"
                                        />

                                        <div class="trash">
                                            <span
                                                @click="
                                                    deleteFile(respuesta.id)
                                                "
                                            >
                                                <feather-icon
                                                    size="18"
                                                    icon="TrashIcon"
                                                ></feather-icon>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
                <div class="footer-estudiantes row">
                    <p class="resultados p-0 m-0">
                        Resultados:
                        {{ filteredEstudiantesSend(respuestas_send).length }}
                        Estudiante(s)
                    </p>
                    <button
                        @click="updateCalificaciones"
                        class="btn-calificacion btn btn-success"
                        :disabled="!respuestas_change"
                    >
                        Subir
                    </button>
                </div>
            </Card>
            <Card class="col-md-12">
                <template #header>
                    <h3><strong>Sin responder</strong></h3>
                </template>
                <div class="container-filtro pb-1 row">
                    <div class="col-sm-8 d-flex align-items-center">
                        <p class="m-0 p-0">
                            Estos son los estudiantes que aún no han enviado su
                            tarea
                        </p>
                    </div>
                    <div class="col-sm-4 m-0 filtro">
                        <input
                            v-model="searchTextUnsend"
                            type="text"
                            class="form-control"
                            placeholder="Buscar por dni, nombre, apellido paterno o materno"
                        />
                    </div>
                </div>
                <div class="row">
                    <ul
                        v-for="estudiante in filteredEstudiantesUnsend(
                            respuestas_unsend
                        )"
                        :key="estudiante.id"
                        class="col-md-12 estudiantes-unsend"
                        :id="'estudiante-' + estudiante.id"
                    >
                        <li>
                            {{ estudiante.numero_documento }} -
                            {{ capitalizeWordSpace(estudiante.nombres) }}
                            {{ capitalizeWord(estudiante.apellido_paterno) }}
                            {{ capitalizeWord(estudiante.apellido_materno) }}
                        </li>
                    </ul>
                </div>
                <div class="footer-estudiantes row">
                    <p class="resultados p-0 m-0">
                        Resultados:
                        {{
                            filteredEstudiantesUnsend(respuestas_unsend).length
                        }}
                        Estudiante(s)
                    </p>
                </div>
            </Card>
            <Modal ref="modaCalificacion">
                <template #title>Nueva sección</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Nota"
                                :modelValue="formRespuesta.puntaje"
                                @click-input="resetInput"
                                :hasError="v$.formRespuesta.puntaje.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'puntaje',
                                            formRespuesta
                                        )
                                "
                                errorMessage="El dato que ingresó es incorrecto."
                            />
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeCalificacion"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-secondary"
                        @click.prevent="resetCalificacion"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Resetear</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeCalificacion"
                    >
                        <feather-icon icon="SaveIcon" />
                        <span>Asignar</span>
                    </button>
                </template>
            </Modal>
        </div>
    </LayoutContent>
</template>

<script>
import useVuelidate from "@vuelidate/core";
import { useForm } from "@inertiajs/vue2";
import {
    capitalizeFirstWord,
    capitalizeFirstWordWithSpace,
} from "../../utils/textProcess";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import Archive from "./components/Archive.vue";
import Modal from "../../Components/Modal.vue";
import InputSchedule from "../Schedule/components/InputSchedule.vue";
import Commentary from "./components/Commentary.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import { alertError, alertSuccess, alertWarning } from "../../sweetAlert2";
import { qualificationsValidator } from "../../Validators/qualificationsValidator";

export default {
    components: {
        LayoutContent,
        Card,
        Archive,
        Modal,
        InputSchedule,
        Commentary,
        ReturnButton,
    },
    name: "QualificationsForum",
    props: {
        urlRoute: String,
        recurso_foro: Object,
        estudiantes: Array,
    },
    data() {
        return {
            visibleEstudiante: [], // IDs de las secciones visibles
            activeDropdown: null, // ID del dropdown activo

            respuestas_send: [],
            respuestas_unsend: [],

            searchTextSend: "", // Texto de búsqueda
            searchTextUnsend: "", // Texto de búsqueda

            estudiantes_respuestas: [],
            respuestas_change: false,

            formRespuesta: useForm({
                id: 0,
                puntaje: "",
            }),
        };
    },
    methods: {
        toggleEstudiante(id) {
            // Alternar la visibilidad de la clase
            if (this.visibleEstudiante.includes(id)) {
                this.visibleEstudiante = this.visibleEstudiante.filter(
                    (claseId) => claseId !== id
                );
            } else {
                this.visibleEstudiante.push(id);
            }
            this.activeDropdown = null; // Cerrar el dropdown activo al abrir/cerrar
        },
        isVisible(id) {
            return this.visibleEstudiante.includes(id); // Verificar si la clase está visible
        },
        beforeEnter(el) {
            el.style.height = "0";
        },
        enter(el) {
            el.style.height = el.scrollHeight + "px";
        },
        leave(el) {
            el.style.height = el.scrollHeight + "px";
            requestAnimationFrame(() => {
                el.style.height = "0";
            });
        },
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        capitalizeWordSpace(text) {
            return capitalizeFirstWordWithSpace(text);
        },
        filteredEstudiantesSend(estudiantes) {
            if (!this.searchTextSend) return estudiantes;
            const searchTextLower = this.searchTextSend.toLowerCase();

            return estudiantes.filter((estudiante) => {
                const fullName =
                    `${estudiante.numero_documento} ${estudiante.nombres} ${estudiante.apellido_paterno} ${estudiante.apellido_materno}`.toLowerCase();
                return fullName.includes(searchTextLower);
            });
        },
        filteredEstudiantesUnsend(estudiantes) {
            if (!this.searchTextUnsend) return estudiantes;
            const searchTextLower = this.searchTextUnsend.toLowerCase();

            return estudiantes.filter((estudiante) => {
                const fullName =
                    `${estudiante.numero_documento} ${estudiante.nombres} ${estudiante.apellido_paterno} ${estudiante.apellido_materno}`.toLowerCase();
                return fullName.includes(searchTextLower);
            });
        },
        getCalificacion(estudiante) {
            const foro =
                estudiante.respuestas[estudiante.respuestas.length - 1];
            const calificacion = this.estudiantes_respuestas.find(
                (calificacion) => calificacion.id === foro.id
            );
            if (calificacion.estado_calificacion === "PENDIENTE") {
                return "S/C";
            }
            return calificacion.puntaje;
        },
        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        resetInput() {
            this.formRespuesta.puntaje = "";
        },
        updateCalificaciones() {
            if (!this.respuestas_change) {
                alertWarning("Aun no haz cambiado ninguna calificación");
                return;
            }

            const data = {
                calificaciones: this.estudiantes_respuestas,
            };

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/recursos-foros-respuestas/update-notas`
                ),
                data: data,
            })
                .then(() => {
                    alertSuccess("Datos Guardados Correctamente");

                    this.$inertia.reload({ only: ["estudiantes"] });

                    this.respuestas_change = false;
                })
                .catch((err) => {
                    console.log(err.response);
                    alertError(err.response.data.message);
                });
        },
        deleteFile(id) {
            console.log("id", id);
            this.$http({
                method: "DELETE",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/recursos-foros-respuestas/destroy/${id}`
                ),
            })
                .then(() => {
                    alertSuccess("Comentario eliminado correctamente");
                    window.location.reload();
                })
                .catch((err) => {
                    console.log(err.response);
                    alertError(err.response.data.message);
                });
        },

        // MODAL

        createCalificacion(id_foro) {
            const foro = this.estudiantes_respuestas.find(
                (calificacion) => calificacion.id === id_foro
            );

            this.formRespuesta.reset();
            this.$refs.modaCalificacion.show();

            this.formRespuesta.id = foro.id;
            this.formRespuesta.puntaje = foro.puntaje;
        },
        closeCalificacion() {
            this.$refs.modaCalificacion.hide();

            this.formRespuesta.reset();
        },
        storeCalificacion() {
            this.v$.formRespuesta.$touch();
            if (this.v$.formRespuesta.$invalid) {
                return;
            }

            const tareaIndex = this.estudiantes_respuestas.findIndex(
                (calificacion) => calificacion.id === this.formRespuesta.id
            );
            if (tareaIndex !== -1) {
                this.estudiantes_respuestas[tareaIndex].puntaje = parseFloat(
                    this.formRespuesta.puntaje
                ).toFixed(2);
                // this.estudiantes_respuestas[tareaIndex].puntaje =
                //     this.formRespuesta.puntaje;
                this.estudiantes_respuestas[tareaIndex].estado_calificacion =
                    "CALIFICADO";
            }

            this.respuestas_change = true;
            this.$refs.modaCalificacion.hide();
            this.formRespuesta.reset();
        },
        resetCalificacion() {
            const tareaIndex = this.estudiantes_respuestas.findIndex(
                (calificacion) => calificacion.id === this.formRespuesta.id
            );
            if (tareaIndex !== -1) {
                this.estudiantes_respuestas[tareaIndex].puntaje = "0.00";
                this.estudiantes_respuestas[tareaIndex].estado_calificacion =
                    "PENDIENTE";
            }

            this.respuestas_change = true;

            this.$refs.modaCalificacion.hide();
        },
        handleBeforeUnload(event) {
            if (this.respuestas_change) {
                const message =
                    "Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?";
                event.returnValue = message; // Estándar para la mayoría de los navegadores
                return message; // Para algunos navegadores
            }
        },
        handlePopState(event) {
            if (this.respuestas_change) {
                const message =
                    "Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?";
                if (!confirm(message)) {
                    event.preventDefault();
                    history.pushState(null, null, location.href); // Evitar el cambio de página
                }
            }
        },
    },
    mounted() {
        // Manejar cierre de pestaña o ventana
        window.addEventListener("beforeunload", this.handleBeforeUnload);

        // Manejar navegación del historial (flecha atrás)
        window.addEventListener("popstate", this.handlePopState);

        history.pushState(null, null, location.href);

        this.respuestas_send = this.estudiantes.filter(
            (estudiante) => estudiante.respuestas.length > 0
        );

        this.respuestas_unsend = this.estudiantes.filter(
            (estudiante) => estudiante.respuestas.length === 0
        );

        this.estudiantes_respuestas = this.estudiantes
            .map((estudiante) => {
                const ultimoForo =
                    estudiante.respuestas[estudiante.respuestas.length - 1];
                return ultimoForo
                    ? {
                          id: ultimoForo.id,
                          estado_calificacion: ultimoForo.estado_calificacion,
                          puntaje: ultimoForo.puntaje,
                      }
                    : null;
            })
            .filter((tarea) => tarea !== null);
    },
    beforeDestroy() {
        window.removeEventListener("beforeunload", this.handleBeforeUnload);
        window.removeEventListener("popstate", this.handlePopState);
    },
    setup() {
        const v$ = useVuelidate();

        return {
            v$,
        };
    },
    validations() {
        return {
            formRespuesta: {
                puntaje: {
                    qualificationsValidator,
                },
            },
        };
    },
};
</script>
<style scoped>
.contenidos {
    border-top: 1px solid #e2e2e2;
}

.acordeon {
    position: relative;
}

.acordeon-container {
    border-bottom: 1px solid #e2e2e2;
}

.acordeon-header {
    padding: 1rem 0;
}

.acordeon-header__title {
    align-items: center;
    gap: 10px;

    h4 {
        font-size: 15px;
    }
}

.acordeon-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    padding-bottom: 1.5rem;
}

.estudiantes-unsend {
    padding-left: 30px;
}

.container-filtro {
    row-gap: 12px;
}

.filtro {
    display: flex;
    align-items: center;

    margin: 10px 10px;
}

.footer-estudiantes {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 12px;
    margin: 0;
    .resultados {
        font-size: 11px;
    }
}

.archivo-container {
    display: flex;
    gap: 10px;

    .trash {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 4%;
        min-width: 50px;
        border-radius: 5px;
        background-color: #ed1a1a;
        border: 1px solid #c40000;
        color: white;
    }

    .trash:hover {
        background-color: #ff3e3e;
        cursor: pointer;
    }
}

.btn-calificacion {
    font-size: 12px;
    padding: 10px 15px;
    min-width: 70px;
}

/* Transiciones */
.slide-enter-active,
.slide-leave-active {
    transition: height 0.2s ease, opacity 0.2s ease;
}

.slide-enter,
.slide-leave-to {
    opacity: 0;
}
</style>
