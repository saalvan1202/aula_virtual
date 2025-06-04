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
        </div>

        <section class="gestion-cursos">
            <CardCursoInfo :curso="curso" path="evaluacion" :uuid="uuid" />

            <div class="row mt-1">
                <div class="col-12">
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
                            @click="setPanel(item.path)"
                        >
                            <CardCourse>
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
                                                icon="InfoIcon"
                                                class="mr-50"
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
            </div>
        </section>
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
    },
    name: "Module",
    props: {
        curso: Object,
        urlRoute: String,
        institutos: Array,
        secciones: Array,
        turnos: Array,
        ciclos: Array,
        periodo: Array,
        uuid: String,
    },
    data() {
        return {
            contenido: [
                {
                    key: 1,
                    nombre: "ACTIVIDADES",
                    descripcion: "Material Para la Únidad Didáctica",
                    icon: "BookOpenIcon",
                    div: false,
                    path: "virtual-classroom",
                },

                {
                    key: 3,
                    nombre: "CLASES",
                    descripcion: "Programación de Clases",
                    icon: "CalendarIcon",
                    div: false,
                    path: "clases",
                },
                {
                    key: 6,
                    nombre: "INDICADORES",
                    descripcion: "Registro de Indicadores",
                    icon: "BarChart2Icon",
                    div: false,
                    path: "indicadores",
                },
                {
                    key: 2,
                    nombre: "ESTUDIANTES",
                    descripcion: "Lista de Matriculados",
                    icon: "UserIcon",
                    div: false,
                    path: "matriculados",
                },

                // {
                //     key: 5,
                //     nombre: "EVALUACIONES",
                //     descripcion: "Registro de Evaluaciones",
                //     icon: "EditIcon",
                //     div: false,
                //     path: "",
                // },
            ],
            loadingCursos: false,
            cursos: [],
            id_periodo: "",
            id_seccion: "",
            id_ciclo: "",
            id_turno: "",
            columns: [
                {
                    data: null,
                    title: "#",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, full, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                },
                { data: "instituto", title: "Instituto" },
                { data: "descripcion", title: "Sede" },
            ],
            form: useForm({
                id: "-1",
                id_instituto: 0,
                descripcion: "",
            }),
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
        store() {
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    alertSuccess("Notas Guardadas Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                });
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
        edit() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            this.form.reset();
            this.$refs.btnEdit.load();
            this.$http
                .get(this.routeTo(`${this.urlRoute}/${row.id}/edit`))
                .then((res) => {
                    this.setForm(res.data);
                    this.$refs.modal.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit.reset();
                });
        },
        destroy() {
            const row = this.$refs.datatable.getSelectedRow();
            if (row == null) {
                alertWarning("Seleccione un registro");
                return;
            }
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    this.$refs.btnDestroy.load();
                    this.$http
                        .delete(this.routeTo(`${this.urlRoute}/${row.id}`))
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$refs.datatable.reload();
                        })
                        .catch((error) => {})
                        .finally(() => {
                            this.$refs.btnDestroy.reset();
                        });
                }
            );
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors || {};
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
