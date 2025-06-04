<template>
    <LayoutContent>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <b-card no-body>
                    <b-tabs card pills>
                        <!-- <b-tab>
                            <template #title>
                                <feather-icon icon="BookOpenIcon" />
                                <span>Calificar</span>
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
                                <BSpinner
                                    variant="success"
                                    type="grow"
                                ></BSpinner>
                            </div>
                            <TableCalificarDocente :indicadores="indicadores" />
                        </b-tab> -->
                        <b-tab>
                            <template #title>
                                <feather-icon icon="ClipboardIcon" />
                                <span>Promedios</span>
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
                                <BSpinner
                                    variant="success"
                                    type="grow"
                                ></BSpinner>
                            </div>
                            <TableCalificacionesDocentes
                                :estudiantes="estudiantes"
                                :curso="curso"
                                v-if="estudiantes"
                            />
                        </b-tab>
                        <b-tab>
                            <template #title>
                                <feather-icon icon="ApertureIcon" />
                                <span>Resúmen</span>
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
                                <BSpinner
                                    variant="success"
                                    type="grow"
                                ></BSpinner>
                            </div>
                            <TableResumen :resumen="resumen" />
                        </b-tab>
                    </b-tabs>
                </b-card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import { BSpinner } from "bootstrap-vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import TableCalificarDocente from "./components/TableCalificarDocente.vue";
import TableCalificacionesDocentes from "./components/TableCalificacionesDocentes.vue";
import TableResumen from "./components/TableResumen.vue";

export default {
    name: "CalificarDocente",
    components: {
        LayoutContent,
        TableCalificarDocente,
        BSpinner,
        TableCalificacionesDocentes,
        TableResumen,
    },
    props: {
        uuid: String,
        id_curso_docente: String,
        curso: Object,
    },
    data() {
        return {
            indicadores: [],
            estudiantes: [],
            resumen: {},
            loadingNotas: false,
            cursoView: {},
        };
    },
    mounted() {
        this.getCalificar(this.uuid);
        this.getPromedios(this.uuid);
        this.getResumen(this.id_curso_docente);
    },
    methods: {
        getCalificar(uuid) {
            this.$http
                .get(this.routeTo(`promedios/calficar-criterios/${uuid}`))
                .then((res) => {
                    console.log(res.data);
                    this.indicadores = res.data;
                })
                .catch((error) => {})
                .finally(() => {});
        },
        getPromedios(uuid) {
            this.estudiantes = [];
            this.$http
                .get(this.routeTo(`promedios/${uuid}`))
                .then((res) => {
                    this.estudiantes = res.data;
                })
                .catch((error) => {})
                .finally(() => {});
        },
        getResumen(id) {
            this.$http
                .get(this.routeTo(`promedios/resumen/${id}`))
                .then((res) => {
                    this.resumen = res.data;
                    console.log(res.data);
                })
                .catch((error) => {})
                .finally(() => {});
        },
    },
};
</script>
<style scoped></style>
