<template>
    <LayoutContent>
        <div class="row">
            <div class="col-md-3 col-lg-3">
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
            </div>
            <div class="col-md-9 col-lg-9">
                <h4 class="card-title">Calificar:</h4>

                <!--ESPACIO PARA EL SPIN  -->
                <div class="spinner" v-if="loadingCursos">
                    <BSpinner variant="success" type="grow"></BSpinner>
                </div>
                <div class="not-length" v-if="cursos.length == 0">
                    <feather-icon icon="InboxIcon" />
                    <span class="align-small">Sin cursos asignados</span>
                </div>
                <div class="row cursos" v-if="cursos">
                    <div
                        class="col-md-6 col-lg-6 cursos-docentes"
                        v-for="(item, index) in cursos"
                        :key="index"
                        @click="redirect(item.uuid, item.id)"
                    >
                        <div class="form-group">
                            <CardCourse>
                                <template #header>
                                    <div style="width: 100%">
                                        <h4
                                            class="card-title"
                                            :title="item.descripcion"
                                        >
                                            {{ item.descripcion }}
                                        </h4>
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
                                                <p style="font-weight: bold">
                                                    Ciclo
                                                </p>
                                                <BBadge variant="light-success">
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
                                                <p style="font-weight: bold">
                                                    Turno
                                                </p>
                                                <BBadge variant="light-success">
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
                                                <p style="font-weight: bold">
                                                    Sección
                                                </p>
                                                <BBadge variant="light-success">
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
        </div>
    </LayoutContent>
</template>
<script>
import Card from "../../Components/Card.vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import TableCalificacionesDocentes from "./components/TableCalificacionesDocentes.vue";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import TableResumen from "./components/TableResumen.vue";
import TableCalificarDocente from "./components/TableCalificarDocente.vue";
import CardCourse from "../../Components/CardCourse.vue";
import { BBadge, BSpinner } from "bootstrap-vue";
import { routeTo } from "../../helpers";
export default {
    name: "NotasDocentes",
    props: {
        uuid: String,
        periodo: Array,
    },
    components: {
        LayoutContent,
        TableCalificacionesDocentes,
        VuePerfectScrollbar,
        Card,
        TableResumen,
        TableCalificarDocente,
        BBadge,
        BSpinner,
        CardCourse,
    },
    data() {
        return {
            active: "",
            actives: 3,
            estudiantes: "",
            resumen: "",
            cursos: "",
            cursoView: "",
            id_periodo: "",
            loadingCursos: false,
            loadingNotas: false,
            indicadores: [],
            criterios: [],
        };
    },
    mounted() {
        this.id_periodo = this.periodo[0].id;
        this.getCursos();
    },
    methods: {
        redirect(uuid, id) {
            this.$inertia.visit(this.routeTo(`calificaciones/${uuid}/${id}`));
        },
        getCursos() {
            this.loadingCursos = true;
            this.$http
                .get(
                    this.routeTo(
                        `aula-virtual/cursos?id_periodo=${this.id_periodo}`
                    )
                )
                .then((res) => {
                    console.log(res.data);
                    this.cursos = res.data;
                    this.loadingCursos = false;
                    this.activeTab(this.cursos[0]);
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                    this.loadingCursos = false;
                });
        },
        activeTab(curso) {
            console.log("curso");
            this.cursoView = curso;
            this.active = curso.id;
            this.getPromedios(curso.uuid);
            this.getResumen(curso.id);
            this.getCalificar(curso.uuid);
        },
        setActive(tab) {
            this.active = tab;
        },
    },
};
</script>
<style scoped>
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
    transition: all 0s ease-in-out, background 0s, color 0s, transform 0.3s,
        border 0.1s;
    border-left: 1px solid #28c76f !important;
    transform: translateY(0);
}
.cursos .card:hover {
    border-left: 4px solid #28c76f !important;
    box-shadow: 0 8px 28px 0 rgba(34, 41, 47, 0.14) !important;
    transform: translateY(-5px);
    cursor: pointer;
}
.info {
    display: flex;
    gap: 2vw;
}
@media (max-width: 405px) {
    .info {
        flex-direction: column;
        gap: 10px;
    }
}
.card-body {
    padding-top: 0 !important;
}
.nav-tabs.nav-link.active {
    color: #01b200 !important;
}
.nav-tabs.nav-link.active,
.nav-tabs.nav-item.show .nav-link {
    color: #01b200 !important;
}
.nav-link:after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(
        30deg,
        #01b200,
        rgb(103 240 177 / 50%)
    ) !important;
}
</style>
