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
                                @change="getAsistencias"
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
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <Card>
                    <template #header>
                        <h4>Listado de Asistencias</h4>
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
                    <TableAsistenciaEstudiantes
                        :asistencias="asistencias"
                        :periodo="periodo"
                        v-else
                    />
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import { BTab, BTabs, BSpinner } from "bootstrap-vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import { FeatherIcon } from "vue-feather-icons";
import Card from "../../Components/Card.vue";
import TableNotasEstudiantes from "./components/TableNotasEstudiantes.vue";
import TableAsistenciaEstudiantes from "./components/TableAsistenciaEstudiantes.vue";
export default {
    data() {
        return {
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
            asistencias: [],
            tipo_asistencia: "",
            id_turno: "",
            id_seccion: "",
            id_ciclo: "",
        };
    },
    props: {
        periodo: Array,
    },
    components: {
        LayoutContent,
        BTab,
        BTabs,
        Card,
        VuePerfectScrollbar,
        TableNotasEstudiantes,
        TableAsistenciaEstudiantes,
        BSpinner,
    },
    name: "AsistenciaEstudiantes",
    created() {
        this.id_periodo = this.periodo[0].id;
    },
    mounted() {
        this.getAsistencias();
    },
    methods: {
        getAsistencias() {
            this.loadingNotas = true;
            this.$http
                .get(this.routeTo(`asistencia/${this.id_periodo}`))
                .then((res) => {
                    this.asistencias = res.data;
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
</style>
