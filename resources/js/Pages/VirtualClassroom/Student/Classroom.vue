<template>
    <LayoutContent>
        <div class="row mt-1">
            <Card class="col-md-12">
                <template #header>
                    <h3>
                        <strong>
                            {{ capitalizeWord(curso.descripcion) }}</strong
                        >
                    </h3>
                </template>
                <div class="title-clases pb-1">
                    <h4 style="width: 20%; display: flex; gap: 5px">
                        <feather-icon
                            icon="BookOpenIcon"
                            style="width: 8%"
                        ></feather-icon>
                        Actividades
                    </h4>
                </div>
                <div
                    class="contenidos d-flex flex-column justify-content-center align-items-center row"
                >
                    <template v-if="clases.length === 0">
                        <div class="py-2">
                            <div
                                class="empty d-flex flex-column justify-content-center align-items-center mt-1"
                            >
                                <div>
                                    <feather-icon icon="InboxIcon" size="50" />
                                </div>
                                <p class="m-0 p-0">
                                    No hay ninguna sección disponible
                                </p>
                            </div>
                        </div>
                    </template>
                    <div
                        v-else
                        v-for="clase in clases"
                        :key="clase.id"
                        class="acordeon-container col-md-12"
                        :id="'clase-' + clase.id"
                    >
                        <div class="acordeon">
                            <div
                                class="acordeon-header d-flex justify-content-between align-items-center"
                            >
                                <div
                                    :id="clase.id"
                                    class="acordeon-header__title d-flex justify-content-start"
                                    @click="toggleClase(clase.id)"
                                    style="cursor: pointer"
                                >
                                    <span>
                                        <BBadge
                                            ref="badgeR"
                                            tabindex="0"
                                            class="badge-arrow"
                                            variant="light-success"
                                            style="
                                                display: grid;
                                                place-items: center;
                                                border-radius: 50%;
                                                height: 40px;
                                                width: 40px;
                                            "
                                        >
                                            <feather-icon
                                                style="
                                                    width: 20px;
                                                    height: 20px;
                                                "
                                                stroke-width="3px"
                                                :icon="
                                                    isVisible(clase.id)
                                                        ? 'ChevronDownIcon'
                                                        : 'ChevronRightIcon'
                                                "
                                            ></feather-icon>
                                        </BBadge>
                                    </span>
                                    <h4 class="m-0 p-0">
                                        {{ capitalizeWord(clase.nombre) }}
                                    </h4>
                                </div>
                            </div>
                            <transition
                                name="slide"
                                @before-enter="beforeEnter"
                                @enter="enter"
                                @leave="leave"
                            >
                                <div
                                    v-show="isVisible(clase.id, clase.nombre)"
                                    class="acordeon-body"
                                >
                                    <template
                                        v-if="
                                            clase.seccion_recurso.length === 0
                                        "
                                    >
                                        <div class="">
                                            <div
                                                class="empty d-flex flex-column justify-content-center align-items-center mt-1"
                                            >
                                                <div>
                                                    <feather-icon
                                                        icon="InboxIcon"
                                                        size="34"
                                                    />
                                                </div>
                                                <p class="m-0 p-0">
                                                    No hay ningun elemento
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                    <div
                                        v-else
                                        v-for="recurso in clase.seccion_recurso"
                                        :key="recurso.id"
                                        :id="'recurso-' + recurso.id"
                                    >
                                        <Activity
                                            :typeActivity="
                                                recurso.tipo_recurso.tipo_nombre.toUpperCase()
                                            "
                                            :color_activity="
                                                recurso.tipo_recurso.color
                                            "
                                            :subTitle="recurso.nombre"
                                            :descripcion="recurso.descripcion"
                                            :id_tipo_recurso="
                                                recurso.id_tipo_recurso
                                            "
                                            :tarea="recurso.recurso_tarea"
                                            :foro="recurso.recurso_foro"
                                            :examen="recurso.recurso_examen"
                                            :tipo="recurso.tipo"
                                            :icon="recurso.tipo_recurso.icono"
                                            :url="recurso.url"
                                            :id="recurso.id"
                                            :id_curso_matricula="
                                                curso.id_curso_matricula
                                            "
                                            :mostrar="recurso.mostrar == 'A'"
                                            :uuid_curso_matricula="curso.uuid"
                                            :studentMode="true"
                                        />
                                    </div>
                                </div>
                            </transition>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
    </LayoutContent>
</template>

<script>
import { FeatherIcon } from "vue-feather-icons";
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import Card from "../../../Components/Card.vue";
import Modal from "../../../Components/Modal.vue";
import AddActivity from "../components/AddActivity.vue";
import Activity from "../components/Activity.vue";
import DropdownMenu from "../components/DropdownMenu.vue";
import InputSchedule from "../../Schedule/components/InputSchedule.vue";
import SelectSchedule from "../../Schedule/components/SelectSchedule.vue";
import { capitalizeFirstWord } from "../../../utils/textProcess";

export default {
    components: {
        LayoutContent,
        Card,
        AddActivity,
        Activity,
        DropdownMenu,
        Modal,
        InputSchedule,
        SelectSchedule,
    },
    name: "Classroom",
    props: {
        urlRoute: String,
        curso: Object,
        secciones: Array,
        tipos_recursos: Array,
        tipos_archivos: Array,
    },
    data() {
        return {
            visibleClases: [], // IDs de las clases visibles
            activeDropdown: null, // ID del dropdown activo
            clases: [],
            page: 0,
            actualDate: "",
        };
    },
    mounted() {
        this.clases = this.secciones || [];
        this.setCurrentDate();
        this.$nextTick(() => {
            this.sections();
        });
    },
    methods: {
        sections() {
            const urlParams = new URLSearchParams(window.location.search);
            const seccion = urlParams.get("seccion");
            if (seccion) {
                console.log(seccion, "as");
                document.getElementById(seccion).click();
            }
        },
        toggleClase(id) {
            // Alternar la visibilidad de la clase
            if (this.visibleClases.includes(id)) {
                this.visibleClases = this.visibleClases.filter(
                    (claseId) => claseId !== id
                );
            } else {
                this.visibleClases.push(id);
            }
            this.activeDropdown = null; // Cerrar el dropdown activo al abrir/cerrar
        },
        isVisible(id) {
            console.log(id);
            return this.visibleClases.includes(id); // Verificar si la clase está visible
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
        setCurrentDate() {
            const date = new Date();
            const formattedDate = date.toISOString().split("T")[0]; // Formatea la fecha a YYYY-MM-DD
            this.actualDate = formattedDate;
        },
    },
    computed: {
        errors() {
            return (
                this.$page.props.errors.clases ||
                this.$page.props.errors.recursos ||
                this.$page.props.errors.recursosTareas ||
                this.$page.props.errors.recursosForos
            );
        },
    },
    watch: {
        secciones: {
            immediate: true, // Ejecutar en el montaje
            handler(newSecciones) {
                this.clases = [...newSecciones];
            },
        },
    },
};
</script>

<style scoped>
.badge-arrow:hover {
    border: 1px solid #28c76f;
}
.badge-arrow:focus {
    outline: 0;
    box-shadow: 0 0 0 0.2rem #28c76f;
}
.title-clases {
    display: flex;
    justify-content: space-between;
}

.contenidos {
    border-radius: 10px;
}

.acordeon {
    position: relative;
}

.acordeon-container {
    border: 1px solid #e2e2e2;
    border-radius: 20px;
    margin-bottom: 15px;
}

.acordeon-header {
    padding: 1.5rem 0;
}

.acordeon-header__title {
    align-items: center;
    gap: 10px;
}

.acordeon-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
    overflow: hidden;
    transition: all 0.3s ease;
    padding-bottom: 1.5rem;
}

.aniadir {
    cursor: pointer;
    font-size: 12px;
}

.aniadir:hover {
    text-decoration: underline;
}

.activity-container {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.activity-item {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding: 10px;
    gap: 4px;

    border: 1px solid #dcdcdc;
}

.activity-item:hover {
    background-color: #f5f5f5;
    cursor: pointer;
}

.activity-item p {
    font-size: 12px;
}

.tmplate {
    padding: 10px 0;
    border-bottom: 2px solid #e0e0e0;
}

.modal-body {
    padding: 0 !important;
}

.empty {
    gap: 4px;
    p {
        font-size: 11px;
    }
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
