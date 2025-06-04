<template>
    <LayoutContent>
        <section class="body-gestion-cursos">
            <div class="row mt-2">
                <div class="col-md-4 col-lg-4">
                    <Card>
                        <template #header>
                            <h4 class="card-title"></h4>
                        </template>
                        <div class="row">
                            <div class="col-md-12 col-lg-12">
                                <div class="form-group">
                                    <label>Periodo de Clases</label>
                                    <select
                                        class="form-control"
                                        v-model="id_periodo"
                                        @change="getCursos"
                                    >
                                        <option
                                            v-for="option in periodo"
                                            :key="option.id"
                                            :value="option.id"
                                        >
                                            {{ option.descripcion }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>
                <div class="col-md-8 col-lg-8">
                    <h4 class="card-title">Mis Cursos</h4>

                    <!--ESPACIO PARA EL SPIN  -->
                    <div class="spinner" v-if="loadingCursos">
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>
                    <div class="not-length" v-if="cursos.length == 0">
                        <feather-icon icon="InboxIcon" />
                        <span class="align-small">Sin cursos matriculados</span>
                    </div>
                    <div class="row cursos" v-if="cursos">
                        <div
                            class="col-md-12 col-lg-12 cursos-docentes"
                            v-for="(item, index) in cursos"
                            :key="index"
                            @click="setGestion(item.uuid)"
                        >
                            <div class="form-group">
                                <CardCourse>
                                    <!-- <span
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
                                        <div style="width: 100%">
                                            <h4
                                                class="card-title"
                                                :title="item.descripcion"
                                            >
                                                {{ item.descripcion }}
                                            </h4>

                                            <div style="width: 100%">
                                                <p
                                                    style="
                                                        padding-top: 8px !important;
                                                    "
                                                >
                                                    {{
                                                        `Clases: ${item.total_desarrolladas} de ${item.total_clases}`
                                                    }}
                                                </p>
                                                <BProgress
                                                    variant="success"
                                                    :max="item.total_clases"
                                                >
                                                    <BProgressBar
                                                        :value="
                                                            item.total_desarrolladas
                                                        "
                                                        :label="`${(
                                                            (item.total_desarrolladas /
                                                                item.total_clases) *
                                                            100
                                                        ).toFixed(2)}%`"
                                                    ></BProgressBar>
                                                </BProgress>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="card-bodyes">
                                        <div class="info">
                                            <section class="info-curso">
                                                <feather-icon
                                                    icon="TrelloIcon"
                                                    class="mr-50"
                                                />
                                                <section class="informacion">
                                                    <p
                                                        style="
                                                            font-weight: bold;
                                                        "
                                                    >
                                                        Ciclo
                                                    </p>
                                                    <BBadge
                                                        variant="light-success"
                                                    >
                                                        {{ item.ciclo }}
                                                    </BBadge>
                                                </section>
                                            </section>
                                            <!-- CICLO -->
                                            <section class="info-curso">
                                                <feather-icon
                                                    icon="WatchIcon"
                                                    class="mr-50"
                                                />
                                                <section class="informacion">
                                                    <p
                                                        style="
                                                            font-weight: bold;
                                                        "
                                                    >
                                                        Turno
                                                    </p>
                                                    <BBadge
                                                        variant="light-success"
                                                    >
                                                        {{ item.turno }}
                                                    </BBadge>
                                                </section>
                                            </section>
                                            <!-- SECCION -->
                                            <section class="info-curso">
                                                <feather-icon
                                                    icon="DiscIcon"
                                                    class="mr-50"
                                                />
                                                <section class="informacion">
                                                    <p
                                                        style="
                                                            font-weight: bold;
                                                        "
                                                    >
                                                        Sección
                                                    </p>
                                                    <BBadge
                                                        variant="light-success"
                                                    >
                                                        {{ item.seccion }}
                                                    </BBadge>
                                                </section>
                                            </section>
                                        </div>
                                    </div>
                                </CardCourse>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12"></div>
            </div>
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import DataTable from "../../Components/DataTable.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
import { BSpinner, BBadge } from "bootstrap-vue";
import CardCourse from "../../Components/CardCourse.vue";
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
        BBadge,
    },
    name: "CursosEstudiantes",
    props: {
        urlRoute: String,
        institutos: Array,
        secciones: Array,
        turnos: Array,
        ciclos: Array,
        periodo: Array,
    },
    data() {
        return {
            loadingCursos: false,
            cursos: [],
            id_periodo: "",
            id_seccion: "",
            id_ciclo: "",
            id_turno: "",
            parents: [],
        };
    },
    created() {
        this.id_periodo = this.periodo[0].id;
    },
    mounted() {
        this.getCursos();
    },
    methods: {
        setGestion(uuid) {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/estudiantes/${uuid}`)
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
            localStorage.setItem("periodo", this.id_periodo);
            this.loadingCursos = true;
            this.$http
                .get(
                    this.routeTo(
                        `${this.urlRoute}/cursos?id_periodo=${this.id_periodo}&turno=${this.id_turno}&seccion=${this.id_seccion}&ciclo=${this.id_ciclo}`
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
                    this.loadingCursos = false;
                });
        },
    },
};
</script>
<style scoped>
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
@keyframes fadeIn {
    form {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.body-gestion-cursos {
    opacity: 0;
    animation: fadeIn 0.5s ease forwards;
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

.cursos-docentes {
    opacity: 0;
    animation: fadeIn 1s ease forwards;
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
.card-bodyes {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 10px;
}
.card-header {
    height: 15vh;
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
.cursos .card {
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1) !important;
    transition: all 0s ease-in-out, background 0s, color 0s, border 0.2s,
        transform 0.6s;
    border-left: 1px solid #28c76f !important;
    transform: translateY(0);
}
.cursos .card:hover {
    border-left: 4px solid #28c76f !important;
    box-shadow: 0 8px 28px 0 rgba(34, 41, 47, 0.14) !important;
    transform: translateY(-10px);
    cursor: pointer;
}
.info {
    display: flex;
    gap: 10vw;
}
@media (max-width: 405px) {
    .info {
        flex-direction: column;
        gap: 10px;
    }
}
</style>
