<template>
    <LayoutContent>
        <ReturnButton
            title="Volver"
            :path_remove="`recursos-examenes/${recurso_examen.id}/calificar`"
        />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <h3><strong>Calificar Examen</strong></h3>
                </template>
                <div class="container-filtro pb-1 row">
                    <div class="col-sm-8">
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
                                        <span
                                            v-if="
                                                estudiante.examen[0]
                                                    .estado_examen ==
                                                'PENDIENTE'
                                            "
                                        >
                                            <strong>(Falta calificar)</strong>
                                        </span>
                                    </h4>
                                </div>
                                <button
                                    v-if="
                                        !visibleEstudiante.includes(
                                            estudiante.id
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
                                            examen, index
                                        ) in estudiante.examen"
                                        :key="examen.id"
                                        class="archivo-container"
                                    >
                                        <Commentary
                                            comentario="Ver respuestas"
                                            :primary="true"
                                            style="width: 100%"
                                            :tag_text="
                                                examen.estado_examen ==
                                                'PENDIENTE'
                                                    ? 'Falta corregir preguntas tipo texto'
                                                    : ''
                                            "
                                            :danger_tag="
                                                examen.estado_examen ==
                                                'PENDIENTE'
                                            "
                                            @click="
                                                redirect(
                                                    `${uuid}/recursos-examenes/${recurso_examen.id}/calificar/${estudiante.id_curso_matricula}`
                                                )
                                            "
                                        />
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
                            examen
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
import InputSchedule from "../Schedule/components/InputSchedule.vue";
import Commentary from "./components/Commentary.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import { qualificationsValidator } from "../../Validators/qualificationsValidator";

export default {
    components: {
        LayoutContent,
        Card,
        Archive,
        InputSchedule,
        Commentary,
        ReturnButton,
    },
    name: "QualificationsExam",
    props: {
        urlRoute: String,
        recurso_examen: Object,
        estudiantes: Array,
        uuid: String,
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
            const examen = estudiante.examen[estudiante.examen.length - 1];
            console.log("examen", examen);
            const calificacion = this.estudiantes_respuestas.find(
                (calificacion) => calificacion.id === examen.id
            );
            console.log("calificacion", calificacion);
            console.log("estado_examen", calificacion.estado_examen);
            if (calificacion.estado_examen === "PENDIENTE") {
                return `N. Temporal: ${calificacion.puntaje} / 20.00`;
            }
            return calificacion.puntaje;
        },
        updateField(value, field, form) {
            this.$set(form, field, value);
        },
        resetInput() {
            this.formRespuesta.puntaje = "";
        },
        redirect(go = "") {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/virtual-classroom/${go}`)
            );
        },
    },
    mounted() {
        this.respuestas_send = this.estudiantes.filter(
            (estudiante) => estudiante.examen.length > 0
        );

        this.respuestas_unsend = this.estudiantes.filter(
            (estudiante) => estudiante.examen.length === 0
        );

        this.estudiantes_respuestas = this.estudiantes
            .map((estudiante) => {
                const ultimoForo =
                    estudiante.examen[estudiante.examen.length - 1];
                return ultimoForo
                    ? {
                          id: ultimoForo.id,
                          estado_examen: ultimoForo.estado_examen,
                          puntaje: ultimoForo.puntaje,
                      }
                    : null;
            })
            .filter((examen) => examen !== null);
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
