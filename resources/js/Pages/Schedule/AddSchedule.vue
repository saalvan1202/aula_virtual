<template>
    <LayoutContent>
        <section>
            <div class="row mt-1">
                <form class="form form-vertical col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                {{
                                    horarios_edit_mode
                                        ? "Lista de horarios"
                                        : "Crear horarios"
                                }}
                            </h4>
                        </template>
                        <div v-show="!horarios_edit_mode" class="row">
                            <div class="col-md-6 col-lg-6">
                                <SelectSchedule
                                    label="Periodo de clases"
                                    :options="periodo_clases"
                                    :modelValue="
                                        formEncabezado.id_periodo_clases
                                    "
                                    :hasError="
                                        v$.formEncabezado.id_periodo_clases
                                            .$error
                                    "
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_periodo_clases',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="El periodo de clases es obligatorio."
                                />
                                <SelectSchedule
                                    label="Sección"
                                    :options="secciones"
                                    :modelValue="formEncabezado.id_seccion"
                                    :hasError="
                                        v$.formEncabezado.id_seccion.$error
                                    "
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_seccion',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="La sección es obligatoria."
                                />
                                <SelectSchedule
                                    label="Sede"
                                    :options="sedes"
                                    :modelValue="formEncabezado.id_sede"
                                    :hasError="v$.formEncabezado.id_sede.$error"
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_sede',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="La sede es obligatoria."
                                    :onChange="fetchProgramaEstudios"
                                />
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <SelectSchedule
                                    label="Turno"
                                    :options="turnos"
                                    :modelValue="formEncabezado.id_turno"
                                    :hasError="
                                        v$.formEncabezado.id_turno.$error
                                    "
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_turno',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="El turno es obligatorio."
                                />
                                <SelectSchedule
                                    label="Docente"
                                    :options="docentes"
                                    :modelValue="formEncabezado.id_docente"
                                    :hasError="
                                        v$.formEncabezado.id_docente.$error
                                    "
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'id_docente',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="El docente es obligatorio."
                                    :onChange="resetUnidadDidactica"
                                    :customizeOptions="true"
                                >
                                    <template #default>
                                        <option
                                            v-for="docente in docentes"
                                            :key="docente.id"
                                            :value="docente.id"
                                        >
                                            {{
                                                docente.persona
                                                    .numero_documento +
                                                " - " +
                                                docente.persona.nombres +
                                                " " +
                                                docente.persona
                                                    .apellido_paterno +
                                                " " +
                                                docente.persona.apellido_materno
                                            }}
                                        </option>
                                    </template>
                                </SelectSchedule>
                                <InputSchedule
                                    label="Unidad didactica"
                                    :modelValue="
                                        formEncabezado.unidad_didactica
                                    "
                                    :hasError="
                                        v$.formEncabezado.unidad_didactica
                                            .$error
                                    "
                                    @update:modelValue="
                                        (value) =>
                                            updateField(
                                                value,
                                                'unidad_didactica',
                                                formEncabezado
                                            )
                                    "
                                    errorMessage="La unidad didactica es obligatoria."
                                    :readOnly="true"
                                    :withButton="true"
                                >
                                    <template #default>
                                        <button
                                            class="btn btn-primary"
                                            type="button"
                                            @click.prevent="
                                                createUnidadDidactica
                                            "
                                        >
                                            <feather-icon
                                                icon="PlusIcon"
                                                class="mr-50"
                                            />
                                        </button>
                                    </template>
                                </InputSchedule>
                            </div>
                        </div>
                        <div class="border rounded">
                            <div
                                class="d-flex justify-content-start align-items-center border-bottom p-1"
                            >
                                <div
                                    class="d-flex justify-content-center align-items-center mr-2"
                                >
                                    Sesiones
                                </div>
                                <div>
                                    <button
                                        class="btn btn-primary"
                                        @click.prevent="createSesiones"
                                    >
                                        <feather-icon
                                            icon="PlusIcon"
                                            class="mr-50"
                                        />
                                        <span class="align-middle">Nuevo</span>
                                    </button>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <SimpleTable
                                    :columns="columns_sesiones"
                                    :data="
                                        horarios_edit_mode
                                            ? horariosToEdit
                                            : sesiones
                                    "
                                    :emptyText="'Agregar sesiones'"
                                >
                                    <template
                                        #acciones="{ row, index }"
                                        class="gap-2"
                                    >
                                        <button
                                            class="btn btn-primary btn-sm"
                                            @click.prevent="
                                                horarios_edit_mode
                                                    ? editHorario(index)
                                                    : editSesion(index)
                                            "
                                        >
                                            <feather-icon icon="EditIcon" />
                                        </button>
                                        <button
                                            class="btn btn-danger btn-sm"
                                            @click.prevent="deleteSesion(index)"
                                        >
                                            <feather-icon icon="TrashIcon" />
                                        </button>
                                    </template>
                                </SimpleTable>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="d-flex justify-content-center">
                                <template v-if="!horarios_edit_mode">
                                    <Button
                                        ref="btnEdit"
                                        class="btn btn-outline-danger mr-1"
                                        @click.native.prevent="exitCreate"
                                    >
                                        <feather-icon
                                            icon="XIcon"
                                            class="mr-50"
                                        />
                                        <span class="align-middle"
                                            >Cancelar</span
                                        >
                                    </Button>
                                    <button
                                        class="btn btn-success ml-1"
                                        @click.prevent="storeHorarios"
                                    >
                                        <feather-icon
                                            icon="SaveIcon"
                                            class="mr-50"
                                        />
                                        <span class="align-middle"
                                            >Guardar</span
                                        >
                                    </button>
                                </template>
                                <template v-else>
                                    <button
                                        class="btn btn-success ml-1"
                                        @click.prevent="exitCreate"
                                    >
                                        <feather-icon
                                            icon="CheckIcon"
                                            class="mr-50"
                                        />
                                        <span class="align-middle">Listo</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </Card>
                </form>
            </div>
            <Modal ref="modalUnidadDidactica">
                <template #title>Seleccionar unidad didactica</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Programa de estudios"
                                :options="programa_estudios"
                                :modelValue="
                                    formUnidadDidactica.id_programa_estudio
                                "
                                :hasError="
                                    v$.formUnidadDidactica.id_programa_estudio
                                        .$error
                                "
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'id_programa_estudio',
                                            formUnidadDidactica
                                        )
                                "
                                errorMessage="El programa de estudios es obligatorio."
                                :disabled="formEncabezado.id_sede == 0"
                                :onChange="fetchPlanesEstudios"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Plan de estudios"
                                :options="planes_estudios"
                                :modelValue="
                                    formUnidadDidactica.id_plan_estudio
                                "
                                :hasError="
                                    v$.formUnidadDidactica.id_plan_estudio
                                        .$error
                                "
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'id_plan_estudio',
                                            formUnidadDidactica
                                        )
                                "
                                errorMessage="El plan de estudios es obligatorio."
                                :disabled="
                                    formUnidadDidactica.id_programa_estudio == 0
                                "
                                :onChange="fetchCursos"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Semestre"
                                :options="ciclos"
                                :modelValue="formUnidadDidactica.id_ciclo"
                                :hasError="
                                    v$.formUnidadDidactica.id_ciclo.$error
                                "
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'id_ciclo',
                                            formUnidadDidactica
                                        )
                                "
                                errorMessage="El semestre es obligatorio."
                                :onChange="fetchCursos"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Unidad didactica"
                                :options="cursos"
                                :modelValue="formUnidadDidactica.id_cursos"
                                :hasError="
                                    v$.formUnidadDidactica.id_cursos.$error
                                "
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'id_cursos',
                                            formUnidadDidactica
                                        )
                                "
                                errorMessage="La unidad didactica es obligatoria."
                                :disabled="
                                    formUnidadDidactica.id_ciclo == 0 &&
                                    formUnidadDidactica.id_plan_estudio == 0
                                "
                            />
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeUnidadDidactica"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeUnidadDidactica"
                        :disabled="formUnidadDidactica.processing"
                    >
                        <feather-icon icon="PlusIcon" />
                        <span>Agregar</span>
                    </button>
                </template>
            </Modal>
            <Modal ref="modalSesiones">
                <template #title>Nueva sesión</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Tipo de aula"
                                :options="tipo_ambientes"
                                :modelValue="formSesiones.tipo_aula_data"
                                :hasError="
                                    v$.formSesiones.tipo_aula_data.$error
                                "
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'tipo_aula_data',
                                            formSesiones
                                        )
                                "
                                errorMessage="El tipo de aula es obligatorio."
                                :onChange="fetchAmbientes"
                                :customizeOptions="true"
                            >
                                <template #default>
                                    <option
                                        v-for="tipo_ambiente in tipo_ambientes"
                                        :key="tipo_ambiente.id"
                                        :value="
                                            JSON.stringify({
                                                id: tipo_ambiente.id,
                                                descripcion:
                                                    tipo_ambiente.descripcion,
                                            })
                                        "
                                    >
                                        {{ tipo_ambiente.descripcion }}
                                    </option>
                                </template>
                            </SelectSchedule>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Aula"
                                :options="ambientes"
                                :modelValue="formSesiones.id_aula"
                                :hasError="v$.formSesiones.id_aula.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'id_aula',
                                            formSesiones
                                        )
                                "
                                errorMessage="El aula es obligatorio."
                                :onChange="updateAforo"
                                :disabled="formSesiones.tipo_aula_data == 0"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Aforo"
                                :modelValue="formSesiones.aforo"
                                :hasError="v$.formSesiones.aforo.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'aforo',
                                            formSesiones
                                        )
                                "
                                errorMessage="El aforo es obligatorio."
                                :readOnly="true"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Día"
                                :options="[
                                    { id: 'LUNES', descripcion: 'LUNES' },
                                    { id: 'MARTES', descripcion: 'MARTES' },
                                    {
                                        id: 'MIERCOLES',
                                        descripcion: 'MIERCOLES',
                                    },
                                    { id: 'JUEVES', descripcion: 'JUEVES' },
                                    { id: 'VIERNES', descripcion: 'VIERNES' },
                                    { id: 'SABADO', descripcion: 'SABADO' },
                                ]"
                                :modelValue="formSesiones.dia"
                                :hasError="v$.formSesiones.dia.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(value, 'dia', formSesiones)
                                "
                                errorMessage="El día es obligatorio."
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectSchedule
                                label="Tipo de clase"
                                :options="[
                                    { id: 'TEORIA', descripcion: 'TEORIA' },
                                    { id: 'PRACTICA', descripcion: 'PRACTICA' },
                                ]"
                                :modelValue="formSesiones.tipo"
                                :hasError="v$.formSesiones.tipo.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(value, 'tipo', formSesiones)
                                "
                                errorMessage="El tipo de clase es obligatorio."
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Hora inicio"
                                type="time"
                                :modelValue="formSesiones.hora_inicio"
                                :hasError="v$.formSesiones.hora_inicio.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'hora_inicio',
                                            formSesiones
                                        )
                                "
                                errorMessage="La hora de inicio es obligatoria (minimo 7:00, maximo 23:00)."
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <InputSchedule
                                label="Hora fin"
                                type="time"
                                :modelValue="formSesiones.hora_fin"
                                :hasError="v$.formSesiones.hora_fin.$error"
                                @update:modelValue="
                                    (value) =>
                                        updateField(
                                            value,
                                            'hora_fin',
                                            formSesiones
                                        )
                                "
                                errorMessage="La hora de fin es obligatoria (minimo 7:00, maximo 23:00)."
                            />
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeSesiones"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeSesiones"
                        :disabled="formSesiones.processing"
                    >
                        <feather-icon icon="PlusIcon" />
                        <span>{{ sesionEditMode ? "Editar" : "Agregar" }}</span>
                    </button>
                </template>
            </Modal>
        </section>
    </LayoutContent>
</template>
<script>
import Vue, { reactive } from "vue";
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import SimpleTable from "../../Components/SimpleTable.vue";
import SelectSchedule from "./components/SelectSchedule.vue";
import InputSchedule from "./components/InputSchedule.vue";
import {
    alertSuccess,
    alertWarning,
    confirm,
    alertError,
} from "../../sweetAlert2.js";
import { routeTo } from "../../helpers.js";
import useVuelidate from "@vuelidate/core";
import { requiredSchedule } from "../../Validators/addScheduleValidator.js";
import { requiredScheduleTime } from "../../Validators/timeValidator.js";
import { subtractTimes, timeToMinutes } from "../../utils/timeProcess.js";

export default {
    components: {
        LayoutContent,
        Card,
        Modal,
        InputError,
        Button,
        SimpleTable,
        SelectSchedule,
        InputSchedule,
    },
    name: "Institute",
    props: {
        urlRoute: String,
        tipo_ambientes: Array,
        periodo_clases: Array,
        secciones: Array,
        sedes: Array,
        turnos: Array,
        docentes: Array,
        ciclos: Array,
        horarios_edit_mode: Boolean,
        horarios_to_edit: Array,
        curso_docente_to_edit: Number,
    },
    data() {
        return {
            // MODE
            sesionEditMode: false,
            keyEdit: 0,
            keyHorarioEdit: 0,
            horariosEditMode: this.horarios_edit_mode,

            // SOLCIITUDES
            ambientes: [],
            programa_estudios: [],
            planes_estudios: [],
            cursos: [],
            horariosToEdit: this.horarios_to_edit,
            idHorarioToEdit: 0,
            cursoDocenteToEdit: this.curso_docente_to_edit,

            // LOCALES
            sesiones: [],

            columns_sesiones: [
                {
                    data: null,
                    title: "#",
                    render: function (data, type, full, meta) {
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },
                },
                { data: "tipo_aula", title: "Tipo aula" },
                { data: "aula", title: "Aula" },
                { data: "aforo", title: "Aforo" },
                { data: "dia", title: "Día" },
                { data: "tipo", title: "Tipo de clase" },
                { data: "hora_inicio", title: "Hora de inicio" },
                { data: "hora_fin", title: "Hora de fin" },
                { data: "acciones", title: "Acciones" },
            ],

            // FORMULARIOS
            formHorario: useForm({
                cursos_docentes: {},
                horarios: [],
            }),

            formUnidadDidactica: useForm({
                id_programa_estudio: 0,
                id_plan_estudio: 0,
                id_ciclo: 0,
                id_cursos: 0,
            }),

            formSesiones: useForm({
                tipo_aula_data: "0",
                id_aula: 0,
                aforo: 0,
                dia: "0",
                tipo: "0",
                hora_inicio: "",
                hora_fin: "",
            }),

            // RESTRICTIONS
            errors: {},
            minHora: "07:00",
            maxHora: "23:00",
        };
    },
    validations() {
        return {
            formEncabezado: {
                id_periodo_clases: {
                    requiredSchedule,
                },
                id_seccion: {
                    requiredSchedule,
                },
                id_sede: {
                    requiredSchedule,
                },
                id_turno: {
                    requiredSchedule,
                },
                id_docente: {
                    requiredSchedule,
                },
                unidad_didactica: {
                    requiredSchedule,
                },
            },

            formUnidadDidactica: {
                id_programa_estudio: {
                    requiredSchedule,
                },
                id_plan_estudio: {
                    requiredSchedule,
                },
                id_ciclo: {
                    requiredSchedule,
                },
                id_cursos: {
                    requiredSchedule,
                },
            },

            formSesiones: {
                tipo_aula_data: {
                    requiredSchedule,
                },
                id_aula: {
                    requiredSchedule,
                },
                aforo: {
                    requiredSchedule,
                },
                dia: {
                    requiredSchedule,
                },
                tipo: {
                    requiredSchedule,
                },
                hora_inicio: {
                    requiredSchedule,
                    requiredScheduleTime,
                },
                hora_fin: {
                    requiredSchedule,
                    requiredScheduleTime,
                },
            },
        };
    },
    setup() {
        const v$ = useVuelidate();

        // Usa reactive para que el formulario sea reactivo en su totalidad
        const formEncabezado = reactive(
            useForm({
                id_periodo_clases: 0,
                id_seccion: 0,
                id_sede: 0,
                id_turno: 0,
                id_docente: 0,
                unidad_didactica: "",
            })
        );

        return {
            v$,
            formEncabezado,
        };
    },
    methods: {
        // UPDATE
        updateField(value, field, form) {
            this.$set(form, field, value); // Usamos $set para asegurar reactividad
        },

        // ARRAY METHODS
        deleteSesion(index) {
            confirm(
                {
                    title: "Confirmar eliminación",
                    text: "¿Está seguro de eliminar la sesión?",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar",
                },
                (result) => {
                    if (result.value) {
                        // Elimina del array de sesiones el objeto que coincida con el index
                        if (this.horariosEditMode) {
                            this.deleteHorario(index);
                        } else {
                            this.sesiones.splice(index, 1);
                            this.formHorario.horarios.splice(index, 1);
                        }
                    }
                }
            );
        },

        async editSesion(index) {
            this.sesionEditMode = true;
            this.keyEdit = index;
            const sesion = this.sesiones[index];

            this.formSesiones.tipo_aula_data = JSON.stringify({
                id: sesion.id_tipo_aula,
                descripcion: sesion.tipo_aula,
            });

            this.formSesiones.aforo = sesion.aforo;
            this.formSesiones.dia = sesion.dia;
            this.formSesiones.tipo = sesion.tipo;
            this.formSesiones.hora_inicio = sesion.hora_inicio;
            this.formSesiones.hora_fin = sesion.hora_fin;

            await this.fetchAmbientesEdit(sesion.id_tipo_aula);
            this.formSesiones.id_aula = sesion.id_aula;

            this.$refs.modalSesiones.show();
        },

        async editHorario(id_horario) {
            this.sesionEditMode = true;
            this.keyHorarioEdit = id_horario;
            const horario = this.horariosToEdit.find(
                (horarioToEdit) => horarioToEdit.id_horario === id_horario
            );

            this.idHorarioToEdit = id_horario;

            this.formSesiones.tipo_aula_data = JSON.stringify({
                id: horario.id_tipo_aula,
                descripcion: horario.tipo_aula,
            });

            this.formSesiones.aforo = horario.aforo;
            this.formSesiones.dia = horario.dia;
            this.formSesiones.tipo = horario.tipo;
            this.formSesiones.hora_inicio = horario.hora_inicio;
            this.formSesiones.hora_fin = horario.hora_fin;

            await this.fetchAmbientesEdit(horario.id_tipo_aula);
            this.formSesiones.id_aula = horario.id_aula;

            this.$refs.modalSesiones.show();
        },

        // REDIRECCIÓN

        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${this.urlRoute}/${name}`));
        },

        // FETCH DATA

        fetchAmbientes() {
            this.formSesiones.id_aula = 0;

            if (this.formSesiones.tipo_aula_data) {
                const tipoAula = JSON.parse(this.formSesiones.tipo_aula_data);

                fetch(this.routeTo(`ambientes/by-tipo/${tipoAula.id}`))
                    .then((response) => response.json())
                    .then((data) => {
                        this.ambientes = data;
                    })
                    .catch((error) => {
                        console.error("Error fetching ambientes:", error);
                        alertError("Error al obtener los ambientes");
                    });
            } else {
                this.ambientes = [];
            }
        },

        async fetchAmbientesEdit(idEdit) {
            this.formSesiones.id_aula = 0;

            fetch(this.routeTo(`ambientes/by-tipo/${idEdit}`))
                .then((response) => response.json())
                .then((data) => {
                    this.ambientes = data;
                })
                .catch((error) => {
                    console.error("Error fetching ambientes:", error);
                    alertError("Error al obtener los ambientes");
                });
        },

        fetchProgramaEstudios() {
            this.resetUnidadDidactica();

            if (this.formEncabezado.id_sede) {
                fetch(
                    this.routeTo(
                        `programas_estudios/by-sede/${this.formEncabezado.id_sede}`
                    )
                )
                    .then((response) => response.json())
                    .then((data) => {
                        this.programa_estudios = data;
                    })
                    .catch((error) => {
                        console.error(
                            "Error fetching programa de estudios:",
                            error
                        );
                        alertError(
                            "Error al obtener los programas de estudios"
                        );
                    });
            } else {
                this.programa_estudios = [];
            }
        },

        fetchPlanesEstudios() {
            this.formUnidadDidactica.id_plan_estudio = 0;
            this.formUnidadDidactica.id_ciclo = 0;
            this.formUnidadDidactica.id_cursos = 0;
            if (this.formUnidadDidactica.id_programa_estudio) {
                fetch(
                    this.routeTo(
                        `planes_estudios/by-programa/${this.formUnidadDidactica.id_programa_estudio}`
                    )
                )
                    .then((response) => response.json())
                    .then((data) => {
                        this.planes_estudios = data;
                    })
                    .catch((error) => {
                        console.error(
                            "Error fetching planes de estudios:",
                            error
                        );
                        alertError("Error al obtener los planes de estudios");
                    });
            } else {
                this.planes_estudios = [];
            }
        },

        fetchCursos() {
            this.formUnidadDidactica.id_cursos = 0;
            this.formUnidadDidacticaid_ciclo = 0;

            if (
                this.formUnidadDidactica.id_plan_estudio &&
                this.formUnidadDidactica.id_ciclo
            ) {
                fetch(
                    this.routeTo(
                        `cursos/by-plan-ciclo/${this.formUnidadDidactica.id_plan_estudio}/${this.formUnidadDidactica.id_ciclo}`
                    )
                )
                    .then((response) => response.json())
                    .then((data) => {
                        this.cursos = data;
                    })
                    .catch((error) => {
                        console.error("Error fetching cursos:", error);
                        alertError("Error al obtener los cursos");
                    });
            } else {
                this.cursos = [];
            }
        },

        // VERIFICACIONES

        async verififyCursoDocente(verifyData) {
            try {
                const response = await this.$http({
                    method: "POST",
                    url: this.routeTo("horarios/verify-curso-docente"),
                    data: verifyData,
                });
                return response.data.exists;
            } catch (error) {
                console.log(error);
                alertError("Error al verificar el curso del docente");
                return false;
            }
        },

        async verififyHorarioConflict(verifyData) {
            try {
                const response = await this.$http({
                    method: "POST",
                    url: this.routeTo("horarios/check-horario-conflict"),
                    data: verifyData,
                });
                return response.data;
            } catch (error) {
                console.log(error);
                alertError("Error al verificar el conflicto de horario");
                return false;
            }
        },

        verifyDiferenceTime(time1, time2) {
            const result = subtractTimes(time1, time2);

            if (result > "03:00") {
                return {
                    invalid: true,
                    message:
                        "La diferencia de horas no puede ser mayor a 3 horas",
                };
            } else if (result < "00:00") {
                return {
                    invalid: true,
                    message:
                        "La hora de fin no puede ser menor a la hora de inicio",
                };
            } else {
                return {
                    invalid: false,
                    message: "",
                };
            }
        },

        verifyRangeTimeLocal(newStartTime, newEndTime, newDay) {
            const newStart = timeToMinutes(newStartTime);
            const newEnd = timeToMinutes(newEndTime);

            for (const sesion of this.sesiones) {
                if (sesion.dia !== newDay) {
                    continue;
                }

                const existingStart = timeToMinutes(sesion.hora_inicio);
                const existingEnd = timeToMinutes(sesion.hora_fin);

                if (
                    (newStart >= existingStart && newStart < existingEnd) ||
                    (newEnd > existingStart && newEnd <= existingEnd) ||
                    (newStart <= existingStart && newEnd >= existingEnd)
                ) {
                    return {
                        invalid: true,
                        message: `El rango de horas se cruza con una sesión suya existente en el día ${newDay}`,
                    };
                }
            }

            return {
                invalid: false,
                message: "",
            };
        },

        // ACTUALIZAR CAMPOS

        updateAforo() {
            const selectedAula = this.ambientes.find(
                (aula) => aula.id === this.formSesiones.id_aula
            );
            if (selectedAula) {
                this.formSesiones.aforo = selectedAula.aforo;
            } else {
                this.formSesiones.aforo = "";
            }
        },

        updateUnidadDidactica() {
            const selectedCurso = this.cursos.find(
                (curso) => curso.id === this.formUnidadDidactica.id_cursos
            );
            if (selectedCurso) {
                this.formEncabezado.unidad_didactica =
                    selectedCurso.descripcion;
            } else {
                this.formEncabezado.unidad_didactica = "";
            }
        },

        updateSesiones(data, tipoAulaData) {
            const selectedAula = this.ambientes.find(
                (ambiente) => ambiente.id === data.id_aula
            );

            const { id_aula, ...sesiones } = data;

            const newData = {
                ...sesiones,
                aula: selectedAula.descripcion,
                id_aula,
                id_tipo_aula: tipoAulaData.id,
                tipo_aula: tipoAulaData.descripcion,
            };

            return newData;
        },

        resetUnidadDidactica() {
            this.formUnidadDidactica.id_programa_estudio = 0;
            this.formUnidadDidactica.id_plan_estudio = 0;
            this.formUnidadDidactica.id_ciclo = 0;
            this.formUnidadDidactica.id_cursos = 0;
            this.formEncabezado.unidad_didactica = "";
        },

        async updateHorario(data, id_horario) {
            try {
                await this.$http({
                    method: "PATCH",
                    url: this.routeTo(`horarios/update/${id_horario}`),
                    data: data,
                });
                // return response.data.exists;

                alertSuccess("Horario actualizado correctamente");
                window.location.reload();
                return;
            } catch (error) {
                console.log(error);
                alertError("Error al actualizar horario");
                return false;
            }
        },

        async deleteHorario(id_horario) {
            try {
                await this.$http({
                    method: "PATCH",
                    url: this.routeTo(`horarios/update/${id_horario}`),
                    data: { estado: "I" },
                });
                // return response.data.exists;

                alertSuccess("Horario eliminado correctamente");
                window.location.reload();
                return;
            } catch (error) {
                console.log(error);
                alertError("Error al eliminar horario");
                return false;
            }
        },

        async createHorario(data) {
            try {
                await this.$http({
                    method: "POST",
                    url: this.routeTo("horarios/store-horario"),
                    data: data,
                });
                // return response.data.exists;

                alertSuccess("Horario creado correctamente");
                window.location.reload();
                return;
            } catch (error) {
                console.log(error);
                alertError("Error al crear horario");
                return false;
            }
        },

        // UNIDAD DIDACTICA

        createUnidadDidactica() {
            this.formUnidadDidactica.reset();
            this.v$.formUnidadDidactica.$reset();
            this.$refs.modalUnidadDidactica.show();
        },

        closeUnidadDidactica() {
            this.formUnidadDidactica.reset();
            this.errors = {};
            this.$refs.modalUnidadDidactica.hide();
        },

        async storeUnidadDidactica() {
            this.v$.formUnidadDidactica.$touch();
            if (this.v$.formUnidadDidactica.$invalid) {
                return;
            }

            const { unidad_didactica, id_docente, ...formEncabezado } =
                this.formEncabezado.data();

            const verifyData = {
                id_docente,
                id_cursos: this.formUnidadDidactica.id_cursos,
                id_periodo_clases: this.formEncabezado.id_periodo_clases,
                id_seccion: this.formEncabezado.id_seccion,
            };

            const cursoDocenteExist = await this.verififyCursoDocente(
                verifyData
            );

            if (cursoDocenteExist) {
                alertWarning("El docente ya tiene asignado este curso");
                return;
            }

            this.updateUnidadDidactica();

            const data = {
                ...formEncabezado,
                ...this.formUnidadDidactica.data(),
                id_docente,
                estado: "A",
            };

            this.formHorario.cursos_docentes = data;

            this.$refs.modalUnidadDidactica.hide();
        },

        // SESIONES

        createSesiones() {
            if (!this.horariosEditMode) {
                this.v$.formEncabezado.$touch();
                if (this.v$.formEncabezado.$invalid) {
                    return;
                }
            }

            this.sesionEditMode = false;
            this.formSesiones.reset();
            this.v$.formSesiones.$reset();
            this.$refs.modalSesiones.show();
        },

        closeSesiones() {
            this.$refs.modalSesiones.hide();
        },

        async storeSesiones() {
            this.v$.formSesiones.$touch();
            if (this.v$.formSesiones.$invalid) {
                return;
            }

            const verifyDiferenceTime = this.verifyDiferenceTime(
                this.formSesiones.hora_fin,
                this.formSesiones.hora_inicio
            );

            if (verifyDiferenceTime.invalid) {
                alertWarning(verifyDiferenceTime.message);
                return;
            }

            const verifyTimeRangeLocal = this.verifyRangeTimeLocal(
                this.formSesiones.hora_inicio,
                this.formSesiones.hora_fin,
                this.formSesiones.dia
            );

            if (verifyTimeRangeLocal.invalid) {
                alertWarning(verifyTimeRangeLocal.message);
                return;
            }

            const dataCheckConflict = {
                id_aula: this.formSesiones.id_aula,
                dia: this.formSesiones.dia,
                hora_inicio: this.formSesiones.hora_inicio,
                hora_fin: this.formSesiones.hora_fin,
            };

            const verifyConflict = await this.verififyHorarioConflict(
                dataCheckConflict
            );

            if (verifyConflict.conflict) {
                const {
                    apellido_materno,
                    apellido_paterno,
                    nombres,
                    dia,
                    hora_inicio,
                    hora_fin,
                    telefono,
                    curso,
                } = verifyConflict.conflicting_range;

                const message = `El docente ${nombres} ${apellido_paterno} ${apellido_materno} - ${telefono} tiene un conflicto de horario en el curso ${curso} el día ${dia} de ${hora_inicio} a ${hora_fin}`;

                alertWarning(message, 5500);

                return;
            }

            const { tipo_aula_data, ...formSesiones } =
                this.formSesiones.data();

            const tipoAula = JSON.parse(this.formSesiones.tipo_aula_data);

            const data = {
                ...formSesiones,
                id_tipo_aula: tipoAula.id,
                estado: "A",
            };

            const dataSesiones = this.updateSesiones(formSesiones, tipoAula);

            if (this.sesionEditMode) {
                if (this.horariosEditMode) {
                    const { aula, tipo_aula, id_tipo_aula, ..._dataSesiones } =
                        dataSesiones;

                    this.updateHorario(_dataSesiones, this.idHorarioToEdit);

                    this.sesionEditMode = false;
                    this.$refs.modalSesiones.hide();

                    return;
                }

                Vue.set(this.sesiones, this.keyEdit, dataSesiones);
                Vue.set(this.formHorario.horarios, this.keyEdit, data);
                this.sesionEditMode = false;
                this.$refs.modalSesiones.hide();

                return;
            }

            if (this.horariosEditMode) {
                const { aula, ..._dataSesiones } = dataSesiones;

                const newDataSesiones = {
                    ..._dataSesiones,
                    id_curso_docente: this.cursoDocenteToEdit,
                };

                await this.createHorario(newDataSesiones);

                return;
            }

            this.sesiones.push(dataSesiones);
            this.formHorario.horarios.push(data);
            this.$refs.modalSesiones.hide();
        },

        // HORARIOS
        storeHorarios() {
            if (!this.horariosEditMode) {
                this.v$.formEncabezado.$touch();
                if (this.v$.formEncabezado.$invalid) {
                    return;
                }
            }

            if (this.formHorario.horarios.length === 0) {
                alertWarning("Debe agregar sesiones");
                return;
            }

            const data = this.formHorario.data();

            this.$http({
                method: "POST",
                url: this.routeTo("horarios/store-curso-horario"),
                data: data,
            })
                .then((res) => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.redirect();
                })
                .catch((error) => {
                    console.error("Error al guardar horarios:", error);
                    alertError("Error al guardar los horarios");
                });
        },

        exitCreate() {
            confirm(
                {
                    title: "Confirmar salida",
                    text: "¿Está seguro de salir del formulario?",
                    confirmButtonText: "Aceptar",
                    cancelButtonText: "Cancelar",
                },
                (result) => {
                    if (result.value) {
                        this.redirect();
                    }
                }
            );
        },
    },
};
</script>
