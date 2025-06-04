<template>
    <LayoutContent>
        <ReturnButton
            title="Volver"
            :path_remove="`recursos-tareas/${recurso_tarea.id}`"
        />
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <h3><strong>Calificar Tarea</strong></h3>
                </template>
                <div class="container-filtro pb-1 row">
                    <div class="col-sm-8">
                        <h5>
                            <strong>Tarea: </strong>
                            {{
                                capitalizeWord(
                                    recurso_tarea.seccion_recurso.nombre
                                )
                            }}
                        </h5>
                        <div>
                            <p class="m-0 p-0">
                                {{
                                    capitalizeWord(
                                        recurso_tarea.seccion_recurso
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
                            estudiantes_send
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
                                            estudiante.tareas[
                                                estudiante.tareas.length - 1
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
                                            tarea, index
                                        ) in estudiante.tareas"
                                        :key="tarea.id"
                                        class="archivo-container"
                                    >
                                        <ArchiveView
                                            :id="tarea.archivos.id"
                                            :archivo="tarea.archivos.archivo"
                                            :nombre="tarea.archivos.nombre"
                                            :estudiante="`${estudiante.nombres} ${estudiante.apellido_paterno} ${estudiante.apellido_materno}`"
                                            :secondary="
                                                index !=
                                                estudiante.tareas.length - 1
                                            "
                                            :primary="
                                                index ==
                                                estudiante.tareas.length - 1
                                            "
                                            style="width: 100%"
                                        />
                                        <div
                                            class="download"
                                            title="Descargar"
                                            @click="
                                                downloadFile(tarea.archivos.id)
                                            "
                                        >
                                            <span x>
                                                <feather-icon
                                                    size="18"
                                                    icon="DownloadIcon"
                                                ></feather-icon>
                                            </span>
                                            <Archive
                                                :id="tarea.archivos.id"
                                                :archivo="
                                                    tarea.archivos.archivo
                                                "
                                                :nombre="tarea.archivos.nombre"
                                                :estudiante="`${estudiante.nombres} ${estudiante.apellido_paterno} ${estudiante.apellido_materno}`"
                                                :secondary="
                                                    index !=
                                                    estudiante.tareas.length - 1
                                                "
                                                :primary="
                                                    index ==
                                                    estudiante.tareas.length - 1
                                                "
                                                style="
                                                    width: 100%;
                                                    display: none;
                                                "
                                            />
                                        </div>
                                        <div
                                            class="trash"
                                            title="Eliminar"
                                            @click="deleteFile(tarea.id)"
                                        >
                                            <span>
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
                        {{ filteredEstudiantesSend(estudiantes_send).length }}
                        Estudiante(s)
                    </p>
                    <button
                        @click="updateCalificaciones"
                        class="btn-calificacion btn btn-success"
                        :disabled="!calificaciones_change"
                    >
                        <feather-icon icon="UploadIcon"></feather-icon>
                        <span>Subir</span>
                    </button>
                </div>
            </Card>
            <Card class="col-md-12">
                <template #header>
                    <h3><strong>Sin entregar</strong></h3>
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
                            estudiantes_unsend
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
                            filteredEstudiantesUnsend(estudiantes_unsend).length
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
                                :modelValue="formCalificacion.puntaje"
                                @click-input="resetInput"
                                :hasError="v$.formCalificacion.puntaje.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'puntaje',
                                            formCalificacion
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
import ArchiveView from "./components/ArchiveView.vue";
import Modal from "../../Components/Modal.vue";
import InputSchedule from "../Schedule/components/InputSchedule.vue";
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
        ReturnButton,
        ArchiveView,
    },
    name: "Qualifications",
    props: {
        urlRoute: String,
        recurso_tarea: Object,
        estudiantes: Array,
    },
    data() {
        return {
            visibleEstudiante: [], // IDs de las secciones visibles
            activeDropdown: null, // ID del dropdown activo

            estudiantes_send: [],
            estudiantes_unsend: [],

            searchTextSend: "", // Texto de búsqueda
            searchTextUnsend: "", // Texto de búsqueda

            estudiantes_calificaciones: [],
            calificaciones_change: false,

            formCalificacion: useForm({
                id: 0,
                puntaje: "",
            }),
        };
    },
    methods: {
        downloadFile(id) {
            console.log(id, document.getElementById(id));
            document.getElementById(id).click();
        },
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
            const tarea = estudiante.tareas[estudiante.tareas.length - 1];
            const calificacion = this.estudiantes_calificaciones.find(
                (calificacion) => calificacion.id === tarea.id
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
            this.formCalificacion.puntaje = "";
        },
        updateCalificaciones() {
            if (!this.calificaciones_change) {
                alertWarning("Aun no haz cambiado ninguna calificación");
                return;
            }

            const data = {
                calificaciones: this.estudiantes_calificaciones,
            };

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/recursos-tareas-estudiante/update-notas`
                ),
                data: data,
            })
                .then(() => {
                    alertSuccess("Datos Guardados Correctamente");

                    this.$inertia.reload({ only: ["estudiantes"] });

                    this.calificaciones_change = false;
                })
                .catch((err) => {
                    console.log(err.response);
                    alertError(err.response.data.message);
                });
        },
        deleteFile(id) {
            this.$http({
                method: "DELETE",
                url: this.routeTo(
                    `gestion-cursos/virtual-classroom/recursos-tareas-estudiante/destroy/${id}`
                ),
            })
                .then(() => {
                    alertSuccess("Tarea eliminada correctamente");
                    window.location.reload();
                })
                .catch((err) => {
                    console.log(err.response);
                    alertError(err.response.data.message);
                });
        },

        // MODAL

        createCalificacion(id_tarea) {
            const tarea = this.estudiantes_calificaciones.find(
                (calificacion) => calificacion.id === id_tarea
            );

            this.formCalificacion.reset();
            this.$refs.modaCalificacion.show();

            this.formCalificacion.id = tarea.id;
            this.formCalificacion.puntaje = tarea.puntaje;
        },
        closeCalificacion() {
            this.$refs.modaCalificacion.hide();

            this.formCalificacion.reset();
        },
        storeCalificacion() {
            this.v$.formCalificacion.$touch();
            if (this.v$.formCalificacion.$invalid) {
                return;
            }

            const tareaIndex = this.estudiantes_calificaciones.findIndex(
                (calificacion) => calificacion.id === this.formCalificacion.id
            );
            if (tareaIndex !== -1) {
                this.estudiantes_calificaciones[tareaIndex].puntaje =
                    parseFloat(this.formCalificacion.puntaje).toFixed(2);
                // this.estudiantes_calificaciones[tareaIndex].puntaje =
                //     this.formCalificacion.puntaje;
                this.estudiantes_calificaciones[
                    tareaIndex
                ].estado_calificacion = "CALIFICADO";
            }

            this.calificaciones_change = true;
            this.$refs.modaCalificacion.hide();
            this.formCalificacion.reset();
        },
        resetCalificacion() {
            const tareaIndex = this.estudiantes_calificaciones.findIndex(
                (calificacion) => calificacion.id === this.formCalificacion.id
            );
            if (tareaIndex !== -1) {
                this.estudiantes_calificaciones[tareaIndex].puntaje = "0.00";
                this.estudiantes_calificaciones[
                    tareaIndex
                ].estado_calificacion = "PENDIENTE";
            }

            this.calificaciones_change = true;

            this.$refs.modaCalificacion.hide();
        },
        handleBeforeUnload(event) {
            if (this.calificaciones_change) {
                const message =
                    "Tienes cambios sin guardar. ¿Estás seguro de que quieres salir?";
                event.returnValue = message; // Estándar para la mayoría de los navegadores
                return message; // Para algunos navegadores
            }
        },
        handlePopState(event) {
            if (this.calificaciones_change) {
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

        this.estudiantes_send = this.estudiantes.filter(
            (estudiante) => estudiante.tareas.length > 0
        );

        this.estudiantes_unsend = this.estudiantes.filter(
            (estudiante) => estudiante.tareas.length === 0
        );

        this.estudiantes_calificaciones = this.estudiantes
            .map((estudiante) => {
                const ultimaTarea =
                    estudiante.tareas[estudiante.tareas.length - 1];
                return ultimaTarea
                    ? {
                          id: ultimaTarea.id,
                          estado_calificacion: ultimaTarea.estado_calificacion,
                          puntaje: ultimaTarea.puntaje,
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
            formCalificacion: {
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
        background-color: #ea5455;
        border: 1px solid #ea5455;
        color: white;
    }
    .download {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 4%;
        min-width: 50px;
        border-radius: 5px;
        background-color: #28c76f;
        border: 1px solid #28c76f;
        color: white;
    }

    .trash:hover {
        background-color: #ff3e3e;
        cursor: pointer;
    }
    .download:hover {
        background-color: #1ad16d;
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
