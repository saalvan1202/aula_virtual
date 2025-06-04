<template>
    <LayoutContent>
        <div class="row">
            <div class="col-md-12 col-lg-12 mb-1">
                <Button
                    ref="btnEdit"
                    class="btn btn-secondary"
                    @click.native.prevent="remember"
                >
                    <feather-icon icon="ArrowLeftIcon" class="mr-50" />
                    <span class="align-small">Volver</span>
                </Button>
            </div>

            <section class="row gestion-cursos">
                <CardCursoInfoEstudiante :curso="curso" path="evaluacion" />

                <div class="col-md-8 col-lg-8">
                    <!--ESPACIO PARA EL SPIN  -->
                    <div class="spinner" v-if="loadingCursos">
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>
                    <div class="row">
                        <!-- CONTENIDO -->
                        <div
                            class="col-md-4 col-lg-4"
                            v-for="item in contenido"
                            :key="item.key"
                            @click="
                                item.pathP == true
                                    ? setPanelP(item.path)
                                    : setPanel(
                                          item.pathP ? item.pathP : item.path
                                      )
                            "
                        >
                            <CardCourse>
                                <!-- NOTIFICACIÓN PARA SEÑAL DE NUEVA ACTIVIDAD O CONTENIDO, CON LA CLASE BADGE DE BV -->
                                <!-- <span
                                    v-if="item.badge"
                                    class="badge badge-danger position-absolute"
                                    style="
                                        top: -5px;
                                        right: -5px;
                                        border-radius: 50%;
                                        height: 20px;
                                        width: 20px;
                                    "
                                >
                                    {{ " " }}
                                </span> -->
                                <template #header>
                                    <h5>{{ item.nombre }}</h5>
                                    <img
                                        style="
                                            height: 100px;
                                            width: 100%;
                                            object-fit: contain;
                                        "
                                        :src="asset(`${item.icon}`)"
                                        :alt="item.nombre"
                                        v-if="item.div"
                                    />
                                    <div class="imgContent" v-else>
                                        <feather-icon
                                            :icon="item.icon"
                                            class="mr-50"
                                        />
                                    </div>
                                </template>
                                <div class="card-bodys">
                                    <div>
                                        <section class="info-curso">
                                            <feather-icon
                                                style="padding-right: 0.3rem"
                                                icon="InfoIcon"
                                                class="btn-sm"
                                            />
                                            <section class="informacion">
                                                <p>
                                                    {{ item.descripcion }}
                                                </p>
                                            </section>
                                        </section>
                                    </div>
                                </div>
                            </CardCourse>
                        </div>
                    </div>
                </div>
            </section>
        </div>
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
import { BSpinner } from "bootstrap-vue";
import CardCursoInfo from "./components/CardCursoInfo.vue";
import CardCursoInfoEstudiante from "./components/CardCursoInfoEstudiante.vue";
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
        CardCursoInfo,
        CardCursoInfoEstudiante,
    },
    name: "GestionCursosEstudiantes",
    props: {
        curso: Object,
        urlRoute: String,
        institutos: Array,
        secciones: Array,
        turnos: Array,
        ciclos: Array,
        periodo: Array,
    },
    data() {
        return {
            contenido: [
                {
                    key: 1,
                    nombre: "ACTIVIDADES",
                    descripcion: "Material Para el curso",
                    icon: "BookOpenIcon",
                    div: false,
                    path: "virtual-classroom",
                    badge: true,
                },

                // {
                //     key: 7,
                //     nombre: "CALIFICACIONES",
                //     descripcion: "Programación de Clases",
                //     icon: "CalendarIcon",
                //     div: false,
                //     pathP: true,
                //     path: "calificaciones",
                // },
                {
                    key: 6,
                    nombre: "ASISTENCIA",
                    descripcion: "Registro de Indicadores",
                    icon: "BarChart2Icon",
                    div: false,
                    path: "asistencia",
                    pathP: true,
                },
                {
                    key: 2,
                    nombre: "EXAMENES",
                    descripcion: "Lista de examenes",
                    icon: "EditIcon",
                    div: false,
                    path: "estudiantes/examenes",
                    badge: true,
                },
                {
                    key: 8,
                    nombre: "TAREAS",
                    descripcion: "Lista de tareas",
                    icon: "FolderIcon",
                    div: false,
                    path: "estudiantes/tareas",
                    badge: true,
                },
                {
                    key: 9,
                    nombre: "FOROS",
                    descripcion: "Lista de foros",
                    icon: "BookIcon",
                    div: false,
                    path: "estudiantes/foros",
                    badge: true,
                },
            ],
            loadingCursos: false,
            cursos: [],
            id_periodo: "",
            id_seccion: "",
            id_ciclo: "",
            id_turno: "",
            parents: [],
        };
    },

    methods: {
        remember() {
            this.$inertia.visit(this.routeTo(`aula-virtual`));
        },
        setPanel(url) {
            //let id = parseFloat(this.curso.id);
            let uuid = this.curso.uuid;
            this.$inertia.visit(
                this.routeTo(`${this.urlRoute}/${url}/${uuid}`)
            );
        },
        setPanelP(path) {
            this.$inertia.visit(this.routeTo(`${path}?id=${this.curso.id}`));
        },
        setGestion() {
            this.$inertia.visit(this.routeTo(`gestion-cursos`));
        },
        setAulaVirtual() {
            let uuid = this.curso.uuid;
            this.$inertia.visit(this.routeTo(`virtual-classroom/${uuid}`));
            console.log(this.routeTo(`virtual-classroom/${uuid}`));
        },
        setCursos(uuid, id, id_periodo_clases) {
            this.$inertia.visit(
                //El Id es el de curso_docente
                this.routeTo()
            );
        },
        setForm(data) {
            this.setFormData(this.form, data);
        },
        create() {
            this.form.reset();
            this.form.id_instituto = firstId(this.institutos);
            this.$refs.modal.show();
        },
        close() {
            this.$refs.modal.hide();
        },
        getCursos() {
            this.loadingCursos = true;
            this.$http
                .get(
                    this.routeTo(
                        `${this.urlRoute}/${this.id_periodo}?turno=${this.id_turno}&seccion=${this.id_seccion}&ciclo=${this.id_ciclo}`
                    )
                )
                .then((res) => {
                    console.log(res.data);
                    this.cursos = res.data;
                    this.loadingCursos = false;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                    this.loadingCursos = false;
                });
        },
    },
};
</script>
<style scoped>
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
.card {
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    border-block-end-style: solid;
    border-block-end-width: 1px;
    border-block-end-color: #7367f0;
    padding-bottom: 20px; /* Espacio reservado para evitar salto */
    box-sizing: border-box; /* Incluye borde en el tamaño total */
    transform: translateY(0);
    margin-bottom: 20px;
}
.card:hover {
    transition: all 0.3s ease-in-out, border 0; /* Transición consistente */
    border-block-end-width: 4px;
    margin-block-end: -1px; /* Compensa el aumento del borde */
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.14);
    transform: translateY(-10px);
    cursor: pointer;
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
.silabo {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
@media (max-width: 600px) {
    .datos-curso {
        flex-direction: column;
    }
    .datos-header {
        display: flex;
        width: 100%;
        flex-direction: column;
        align-items: center;
    }
}
@media (max-width: 350px) {
    .silabo {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
}
</style>
