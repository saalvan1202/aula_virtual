<template>
    <LayoutContent>
        <div :class="!viewAula ? 'row' : 'aula-row'">
            <div class="col-md-3 col-lg-3" v-if="!viewAula">
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
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <div class="sidebar-left">
                            <div class="sidebar">
                                <div class="sidebar-content todo-sidebar">
                                    <div class="option-app-menu">
                                        <VuePerfectScrollbar
                                            class="sidebar-menu-list scroll-area"
                                            :settings="{
                                                maxScrollbarLength: 60,
                                            }"
                                        >
                                            <div
                                                style="
                                                    margin-bottom: 0.7rem !important;
                                                "
                                                class="border-bottom-secondary px-2 d-flex justify-content-between"
                                            >
                                                <h6
                                                    class="section-label text-secondary"
                                                    style="font-weight: 600"
                                                >
                                                    CURSOS
                                                </h6>
                                            </div>
                                            <div
                                                class="spinner"
                                                v-if="loadingCursos"
                                                style="
                                                    display: flex;
                                                    justify-content: center;
                                                    align-items: center;
                                                    width: 100%;
                                                "
                                            >
                                                <BSpinner
                                                    variant="success"
                                                    type="grow"
                                                ></BSpinner>
                                            </div>
                                            <div
                                                class="list-group list-group-filters"
                                            >
                                                <a
                                                    v-for="item in cursos"
                                                    :key="item.id"
                                                    class="list-group-item list-group-item-option list-group-item-action"
                                                    @click.prevent="
                                                        activeTab(
                                                            item.id,
                                                            item.id,
                                                            item.id_matricula
                                                        )
                                                    "
                                                    :class="{
                                                        active:
                                                            active == item.id,
                                                    }"
                                                    style="
                                                        text-align: center;
                                                        display: flex;
                                                        padding: 25px;
                                                    "
                                                >
                                                    <span
                                                        class="align-text-bottom line-height-1"
                                                        >{{
                                                            item.descripcion
                                                        }}</span
                                                    >
                                                </a>
                                            </div>
                                        </VuePerfectScrollbar>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-lg-9">
                <Card>
                    <template #header>
                        <h4>Calificaciones</h4>
                    </template>
                    <div
                        class="spinner"
                        v-if="loadingNotas"
                        style="
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            width: 100%;
                        "
                    >
                        <BSpinner variant="success" type="grow"></BSpinner>
                    </div>
                    <TableNotasEstudiantes
                        :notas="notas"
                        v-else
                        :notaFinal="notaFinal"
                    />
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import { BTab, BTabs } from "bootstrap-vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import { FeatherIcon } from "vue-feather-icons";
import Card from "../../Components/Card.vue";
import TableNotasEstudiantes from "./components/TableNotasEstudiantes.vue";
export default {
    data() {
        return {
            viewAula: false,
            id_periodo: "",
            active: "",
            cursos: [],
            loadingCursos: false,
            loadingNotas: false,
            notasView: [
                {
                    id: 1,
                    unidad: "INDICADOR I",
                    porcentaje: 30,
                    nota: "-",
                    criterios: [
                        {
                            nombre: "Paricipaciones",
                            porcentaje: 10,
                            nota: 10,
                            id: 1,
                        },
                        {
                            nombre: "Laboratorio",
                            porcentaje: 25,
                            nota: 15,
                            id: 2,
                        },
                        {
                            nombre: "Examen Parcial I",
                            porcentaje: 30,
                            nota: 12,
                            id: 4,
                        },
                        {
                            nombre: "Producto Académico",
                            porcentaje: 35,
                            nota: 11,
                            id: 3,
                        },
                    ],
                },
                {
                    id: 2,
                    unidad: "INDICADOR II",
                    porcentaje: 30,
                    nota: "-",
                    criterios: [
                        {
                            nombre: "Paricipaciones",
                            porcentaje: 10,
                            nota: 10,
                            id: 1,
                        },
                        {
                            nombre: "Laboratorio",
                            porcentaje: 25,
                            nota: 15,
                            id: 2,
                        },
                        {
                            nombre: "Examen Parcial I",
                            porcentaje: 30,
                            nota: 12,
                            id: 4,
                        },
                        {
                            nombre: "Producto Académico",
                            porcentaje: 35,
                            nota: 11,
                            id: 3,
                        },
                    ],
                },
            ],
            notas: [],
            id_turno: "",
            id_seccion: "",
            id_ciclo: "",
            notaFinal: "",
        };
    },
    props: {
        periodo: Array,
        id_matricula: Number,
    },
    components: {
        LayoutContent,
        BTab,
        BTabs,
        Card,
        VuePerfectScrollbar,
        TableNotasEstudiantes,
    },
    name: "Notas",
    created() {
        this.id_periodo = this.periodo[0].id;
    },
    mounted() {
        this.redirect();
        this.getCursos();
    },
    methods: {
        redirect() {
            const urlParams = new URLSearchParams(window.location.search);
            const id = urlParams.get("id");
            if (id) {
                this.getNotas(id, this.id_matricula);
                this.viewAula = true;
            }
        },
        getCursos() {
            this.loadingCursos = true;
            this.$http
                .get(
                    this.routeTo(
                        `aula-virtual/cursos?id_periodo=${this.id_periodo}&turno=${this.id_turno}&seccion=${this.id_seccion}&ciclo=${this.id_ciclo}`
                    )
                )
                .then((res) => {
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
        getNotas(id, id_matricula) {
            this.loadingNotas = true;
            this.notas = [];
            this.$http
                .get(
                    this.routeTo(
                        `calificaciones/estudiantes/notas/${id}/${id_matricula}`
                    )
                )
                .then((res) => {
                    this.notas = res.data.data;
                    this.notaFinal = res.data.notaFinal;
                    this.loadingNotas = false;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.loadingNotas = false;
                });
        },
        activeTab(tab, id, id_matricula) {
            console.log(id);
            this.getNotas(id, id_matricula);
            this.active = tab;
            this.$emit("active", tab);
        },
        setActive(tab) {
            this.active = tab;
        },
    },
};
</script>
<style scoped>
.nav-pills .nav-link,
.nav-tabs .nav-link {
    justify-content: start !important;
    align-items: start !important;
}
.title-actividades {
    display: flex;
    justify-content: center;
}
.aula-row {
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
}
</style>
