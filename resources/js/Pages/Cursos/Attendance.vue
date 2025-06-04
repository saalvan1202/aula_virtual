<template>
    <LayoutContent>
        <div class="row mt-1">
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
            <div class="col-12">
                <Card>
                    <Spinner v-if="form.processing" />
                    <template #header>
                        <h4 class="card-title">
                            Control Asistencia Estudiantes
                        </h4>
                    </template>
                    <div class="row">
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <p><strong>Clase:</strong></p>
                                <p>{{ clase.descripcion }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <p><strong>Hora:</strong></p>
                                <p>
                                    {{
                                        `${clase.hora_inicio} - ${clase.hora_fin}`
                                    }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-group">
                                <p><strong>Fecha:</strong></p>
                                <p>{{ convertDate(clase.fecha, "-", "/") }}</p>
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3">
                            <div class="form-groups mb-2">
                                <label>Seleccionar Para Todos</label>
                                <select
                                    class="form-control"
                                    v-model="todos"
                                    @change="onTodo"
                                >
                                    <option value="">NO SELECCIONADO</option>
                                    <option value="PRESENTE">PRESENTE</option>
                                    <option value="FALTA">FALTA</option>
                                    <option value="TARDE">TARDE</option>
                                    <option value="JUSTIFICADO">
                                        JUSTIFICADO
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div
                        class="table-responsive"
                        style="height: 380px; overflow-y: auto"
                    >
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 3rem">#</th>
                                    <th>Apellidos Y Nombres</th>
                                    <th>Documento</th>
                                    <th>Faltas</th>
                                    <th title="Presente" style="width: 3rem">
                                        A
                                    </th>
                                    <th title="Falta" style="width: 3rem">F</th>
                                    <th title="Tarde" style="width: 3rem">T</th>
                                    <th title="Justificado" style="width: 3rem">
                                        J
                                    </th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, index) in alumnosView">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.estudiante }}</td>
                                    <td>{{ item.numero_documento }}</td>
                                    <td style="text-align: center">
                                        {{ item.falta }}
                                    </td>
                                    <td>
                                        <input
                                            type="radio"
                                            value="PRESENTE"
                                            v-model="item.tipo_asistencia"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="radio"
                                            value="FALTA"
                                            v-model="item.tipo_asistencia"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="radio"
                                            value="TARDE"
                                            v-model="item.tipo_asistencia"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            type="radio"
                                            value="JUSTIFICADO"
                                            v-model="item.tipo_asistencia"
                                        />
                                    </td>
                                    <td>
                                        <input
                                            class="form-control input-group-sm"
                                            maxlength="100"
                                            v-model="item.observaciones"
                                            v-input-upper
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col text-right">
                            <button
                                class="btn btn-outline-danger"
                                @click="remember"
                            >
                                Cancelar
                            </button>
                            <Button
                                ref="btnStore"
                                class="btn btn-success"
                                @click.native="store"
                                :disabled="form.processing"
                                v-if="culminado != 'S'"
                            >
                                <feather-icon icon="SaveIcon" />
                                <span>Guardar</span>
                            </Button>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import InputError from "../../Components/InputError.vue";
import { alertSuccess, alertWarning } from "../../sweetAlert2";
import Button from "../../Components/Button.vue";
import { currentDate } from "../../helpers";
import Spinner from "../../Components/Spinner.vue";
import { convertDate } from "../../helpers";
export default {
    components: { Spinner, Button, InputError, Card, LayoutContent },
    name: "Attendance",
    props: {
        urlRoute: String,
        periodo_clases: Array,
        id_clase_docente: String,
        alumnos: Array,
        clase: Object,
        uuid: String,
        culminado: String,
        id_curso_docente: Number,
    },
    data() {
        return {
            alumnosView: [],
            form: useForm({
                id_curso_docente: "",
                id_clase_docente: "",
                detalle: [],
            }),
            todos: "",
        };
    },
    mounted() {
        this.alumnosView = this.alumnos;
        console.log(this.alumnosView);
        this.reset();
    },
    methods: {
        convertDate,
        remember() {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/clases/${this.uuid}`)
            );
        },
        reset() {
            this.form.reset();
            this.form.fecha = currentDate();
        },
        onTodo() {
            this.alumnos.map((el) => {
                el.tipo_asistencia = this.todos;
            });
        },
        getAlumnos() {
            this.$refs.btnStore.load();
            this.$http
                .get(
                    this.routeTo(
                        `asistencia/get-estudiantes?id_curso_docente=${this.clase.id_curso_docente}&id_clase_docente=${this.clase.id}`
                    )
                )
                .then((res) => {
                    this.alumnosView = res.data;
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnStore.reset();
                });
        },
        store() {
            if (this.alumnos.length == 0) {
                alertWarning("no hay estudiantes para asistencia");
                return;
            }
            const vacio = this.alumnos.filter((el) => {
                return el.tipo_asistencia === "";
            }).length;
            if (vacio > 0) {
                alertWarning(
                    `Hay ${vacio} alumno(s) que no esta(n) marcado(s)`
                );
                return;
            }
            this.form.id_clase_docente = this.id_clase_docente;
            this.form.id_curso_docente = this.id_curso_docente;
            this.form.detalle = this.alumnosView;
            this.form.processing = true;
            this.$http({
                method: "POST",
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            })
                .then((res) => {
                    this.getAlumnos();
                    alertSuccess("Datos Guardados Correctamente");
                    this.reset();
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.form.processing = false;
                });
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
.form-group {
    display: flex;
    gap: 10px;
}
</style>
