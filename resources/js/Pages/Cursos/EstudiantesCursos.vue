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
        <section class="estudiantes-cursos">
            <CardCursoInfo :curso="curso" :path="path" />
            <Card>
                <template #header>
                    <h4>Matriculados</h4>
                </template>
                <TableStudent :alumnos="estudiantes" :urlRoute="urlRoute" />
            </Card>

            <div class="row mt-1">
                <div class="col-12">
                    <!--ESPACIO PARA EL SPIN  -->
                    <div class="spinner" v-if="loadingCursos">
                        <BSpinner variant="success" type="grow"></BSpinner>
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
import TableStudent from "../../Components/TableStudent.vue";
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
        TableStudent,
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
        estudiantes: Array,
        uuid: String,
    },
    data() {
        return {
            path: "",
            loadingCursos: false,
            cursos: [],
            parents: [],
        };
    },
    mounted() {
        this.path = `gestion-cursos/${this.curso.uuid}`;
    },
    methods: {
        remember() {
            this.$inertia.visit(this.routeTo(`gestion-cursos/${this.uuid}`));
        },
        //Estudiantes Matriculados en el Curso
        setEstudiantesMatriculados() {
            let id = parseFloat(this.curso.id);
            let uuid = this.curso.uuid;
            this.$inertia.visit(
                this.routeTo(`${this.urlRoute}/matriculados/${id}/${uuid}`)
            );
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
</style>
