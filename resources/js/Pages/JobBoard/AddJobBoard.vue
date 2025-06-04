<template>
    <LayoutContent>
        <section>
            <div class="row mt-1">
                <form class="form form-vertical col-12">
                    <Spinner v-if="formPropuesta.processing" />
                    <Card>
                        <template #header>
                            <h4 class="card-title">Crear propuesta laboral</h4>
                        </template>
                        <div class="row">
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Inicio de publicación</label>
                                    <input
                                        class="form-control"
                                        type="date"
                                        v-model="formPropuesta.fecha_inicio"
                                        :class="{
                                            'is-invalid': errors.fecha_inicio,
                                        }"
                                        :min="fechaActual"
                                    />
                                    <InputError :error="errors.fecha_inicio" />
                                </div>
                                <div class="form-group">
                                    <label>Fin de publicación</label>
                                    <input
                                        class="form-control"
                                        type="date"
                                        v-model="formPropuesta.fecha_fin"
                                        :class="{
                                            'is-invalid': errors.fecha_fin,
                                        }"
                                        :min="fechaActual"
                                    />
                                    <InputError :error="errors.fecha_fin" />
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <SelectSearch
                                            ref="empresa"
                                            :class="{
                                                'is-invalid': errors.id_empresa,
                                            }"
                                            :errorInput="errors.id_empresa"
                                            @input="handleEmpresaSelect"
                                        >
                                            <button
                                                class="btn btn-primary"
                                                @click.prevent="createEmpresa"
                                            >
                                                <feather-icon icon="PlusIcon" />
                                            </button>
                                        </SelectSearch>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Representante</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.representante"
                                        :class="{
                                            'is-invalid': errors.representante,
                                        }"
                                        readonly
                                    />
                                    <InputError :error="errors.representante" />
                                </div>
                                <SelectDepartament
                                    :departamentos="departamentos"
                                    :error="errors.id_ubigeo"
                                    :modelValue="cod_dpto"
                                    @update:modelValue="
                                        ($event) => (cod_dpto = $event)
                                    "
                                />
                                <SelectProvince
                                    :error="errors.id_ubigeo"
                                    :modelValue="cod_prov"
                                    @update:modelValue="
                                        ($event) => (cod_prov = $event)
                                    "
                                />
                                <SelectDistrict
                                    :error="errors.id_ubigeo"
                                    :modelValue="formPropuesta.id_ubigeo"
                                    @update:modelValue="
                                        ($event) =>
                                            (formPropuesta.id_ubigeo = $event)
                                    "
                                />
                                <div class="form-group">
                                    <label>Dirección</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.direccion"
                                        :class="{
                                            'is-invalid': errors.direccion,
                                        }"
                                    />
                                    <InputError :error="errors.direccion" />
                                </div>
                                <div class="form-group">
                                    <label>Correo</label>
                                    <input
                                        class="form-control"
                                        type="email"
                                        v-model="formPropuesta.correo"
                                        :class="{
                                            'is-invalid': errors.correo,
                                        }"
                                        readonly
                                    />
                                    <InputError :error="errors.correo" />
                                </div>
                                <div class="form-group">
                                    <label>Telefono</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.telefono"
                                        :class="{
                                            'is-invalid': errors.telefono,
                                        }"
                                        readonly
                                    />
                                    <InputError :error="errors.telefono" />
                                </div>
                                <div class="form-group">
                                    <label>Página web</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.pagina_web"
                                        :class="{
                                            'is-invalid': errors.pagina_web,
                                        }"
                                        readonly
                                    />
                                    <InputError :error="errors.pagina_web" />
                                </div>
                                <div class="form-group">
                                    <label>Programa de estudio</label>
                                    <select
                                        class="form-control"
                                        v-model="
                                            formPropuesta.id_programa_estudio
                                        "
                                        :class="{
                                            'is-invalid':
                                                errors.id_programa_estudio,
                                        }"
                                    >
                                        <option value="0" disabled>
                                            Seleccione una opción
                                        </option>
                                        <option
                                            v-for="programa_estudio in programa_estudios"
                                            :key="programa_estudio.id"
                                            :value="programa_estudio.id"
                                        >
                                            {{ programa_estudio.descripcion }}
                                        </option>
                                    </select>
                                    <InputError
                                        :error="errors.id_programa_estudio"
                                    />
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6">
                                <div class="form-group">
                                    <label>Número de vacantes</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.numero_vacantes"
                                        :class="{
                                            'is-invalid':
                                                errors.numero_vacantes,
                                        }"
                                    />
                                    <InputError
                                        :error="errors.numero_vacantes"
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Puesto</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.puesto"
                                        :class="{
                                            'is-invalid': errors.puesto,
                                        }"
                                    />
                                    <InputError :error="errors.puesto" />
                                </div>
                                <div class="form-group">
                                    <label>Descripción de puesto</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="
                                            formPropuesta.descripcion_puesto
                                        "
                                        :class="{
                                            'is-invalid':
                                                errors.descripcion_puesto,
                                        }"
                                    />
                                    <InputError
                                        :error="errors.descripcion_puesto"
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Modalidad de trabajo</label>
                                    <select
                                        class="form-control"
                                        v-model="
                                            formPropuesta.modalidad_trabajo
                                        "
                                        :class="{
                                            'is-invalid':
                                                errors.modalidad_trabajo,
                                        }"
                                    >
                                        <option value="0">
                                            Selecciona una opción
                                        </option>
                                        <option>PRESENCIAL</option>
                                        <option>HÍBRIDO</option>
                                        <option>REMOTO</option>
                                    </select>
                                    <InputError
                                        :error="errors.modalidad_trabajo"
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Horario</label>
                                    <select
                                        class="form-control"
                                        v-model="formPropuesta.horario"
                                        :class="{
                                            'is-invalid': errors.horario,
                                        }"
                                    >
                                        <option value="0">
                                            Selecciona una opción
                                        </option>
                                        <option>PART-TIME</option>
                                        <option>FULL-TIME</option>
                                    </select>
                                    <InputError :error="errors.horario" />
                                </div>
                                <div class="form-group">
                                    <label>Salario</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.salario"
                                        :class="{
                                            'is-invalid': errors.salario,
                                        }"
                                    />
                                    <InputError :error="errors.salario" />
                                </div>
                                <div class="form-group">
                                    <label>Requisitos</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.requisitos"
                                        :class="{
                                            'is-invalid': errors.requisitos,
                                        }"
                                    />
                                    <InputError :error="errors.requisitos" />
                                </div>
                                <div class="form-group">
                                    <label>Nivel de trabajo</label>
                                    <select
                                        class="form-control"
                                        v-model="formPropuesta.nivel_trabajo"
                                        :class="{
                                            'is-invalid': errors.nivel_trabajo,
                                        }"
                                    >
                                        <option value="0">
                                            Selecciona una opción
                                        </option>
                                        <option>SIN EXPERIENCIA</option>
                                        <option>TRAINEE</option>
                                        <option>JUNIOR</option>
                                        <option>SEMI-SENIOR</option>
                                        <option>SENIOR</option>
                                    </select>
                                    <InputError :error="errors.nivel_trabajo" />
                                </div>
                                <div class="form-group">
                                    <label>Funciones a realizar</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.funciones"
                                        :class="{
                                            'is-invalid': errors.funciones,
                                        }"
                                    />
                                    <InputError :error="errors.funciones" />
                                </div>
                                <div class="form-group">
                                    <label>Link de postulación</label>
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formPropuesta.link_postulacion"
                                        :class="{
                                            'is-invalid':
                                                errors.link_postulacion,
                                        }"
                                    />
                                    <InputError
                                        :error="errors.link_postulacion"
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Disponibilidad para viajar</label>
                                    <select
                                        class="form-control"
                                        v-model="
                                            formPropuesta.disponibilidad_viajar
                                        "
                                        :class="{
                                            'is-invalid':
                                                errors.disponibilidad_viajar,
                                        }"
                                    >
                                        <option value="A">Si</option>
                                        <option value="I">No</option>
                                    </select>
                                    <InputError
                                        :error="errors.disponibilidad_viajar"
                                    />
                                </div>
                                <div class="form-group">
                                    <label>Apto para discapacitados</label>
                                    <select
                                        class="form-control"
                                        v-model="
                                            formPropuesta.apto_discapacitados
                                        "
                                        :class="{
                                            'is-invalid':
                                                errors.apto_discapacitados,
                                        }"
                                    >
                                        <option value="A">Si</option>
                                        <option value="I">No</option>
                                    </select>
                                    <InputError
                                        :error="errors.apto_discapacitados"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="mt-1">
                            <div class="d-flex justify-content-center">
                                <Button
                                    ref="btnEdit"
                                    class="btn btn-outline-danger mr-1"
                                    @click.native.prevent="exitCreate"
                                >
                                    <feather-icon icon="XIcon" class="mr-50" />
                                    <span class="align-middle">Cancelar</span>
                                </Button>
                                <button
                                    class="btn btn-success ml-1"
                                    @click.prevent="storePropuesta"
                                    :disabled="formPropuesta.processing"
                                >
                                    <feather-icon
                                        :icon="
                                            !isEdit ? 'SaveIcon' : 'EditIcon'
                                        "
                                        class="mr-50"
                                    />
                                    <span class="align-middle">{{
                                        !isEdit ? "Guardar" : "Editar"
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </Card>
                </form>
            </div>
            <Modal ref="modalEmpresa" style="z-index: 2050">
                <template #title>Registrar empresa</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Ruc</label>
                                <div
                                    :class="
                                        errors.ruc
                                            ? 'border border-danger rounded input-group input-group-merge'
                                            : 'input-group input-group-merge'
                                    "
                                >
                                    <input
                                        class="form-control"
                                        type="text"
                                        v-model="formEmpresa.ruc"
                                    />
                                    <SearchEmpresa
                                        :ruc="formEmpresa.ruc"
                                        @input="handleEmpresa"
                                    />
                                </div>
                                <InputError :error="errors.ruc" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Razón social</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.razon_social"
                                    :class="{
                                        'is-invalid': errors.razon_social,
                                    }"
                                />
                                <InputError :error="errors.razon_social" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre comercial</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.nombre_comercial"
                                    :class="{
                                        'is-invalid': errors.nombre_comercial,
                                    }"
                                />
                                <InputError :error="errors.nombre_comercial" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Rubro</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.rubro"
                                    :class="{
                                        'is-invalid': errors.rubro,
                                    }"
                                />
                                <InputError :error="errors.rubro" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectDepartament
                                :departamentos="departamentos"
                                :error="errors.id_ubigeo_empresa"
                                :modelValue="cod_dpto_empresa"
                                :index="1"
                                @update:modelValue="
                                    ($event) => (cod_dpto_empresa = $event)
                                "
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectProvince
                                :error="errors.id_ubigeo_empresa"
                                :modelValue="cod_prov_empresa"
                                :index="1"
                                @update:modelValue="
                                    ($event) => (cod_prov_empresa = $event)
                                "
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectDistrict
                                :error="errors.id_ubigeo_empresa"
                                :modelValue="formEmpresa.id_ubigeo_empresa"
                                :index="1"
                                @update:modelValue="
                                    ($event) =>
                                        (formEmpresa.id_ubigeo_empresa = $event)
                                "
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Dirección</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.direccion"
                                    :class="{
                                        'is-invalid': errors.direccion,
                                    }"
                                />
                                <InputError :error="errors.direccion" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Telefono</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.telefono"
                                    :class="{
                                        'is-invalid': errors.telefono,
                                    }"
                                />
                                <InputError :error="errors.telefono" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Representante</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.representante_legal"
                                    :class="{
                                        'is-invalid':
                                            errors.representante_legal,
                                    }"
                                />
                                <InputError
                                    :error="errors.representante_legal"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Correo</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.email"
                                    :class="{
                                        'is-invalid': errors.email,
                                    }"
                                />
                                <InputError :error="errors.email" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Pagina web</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.pagina_web"
                                    :class="{
                                        'is-invalid': errors.pagina_web,
                                    }"
                                />
                                <InputError :error="errors.pagina_web" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripción</label>
                                <input
                                    class="form-control"
                                    type="text"
                                    v-model="formEmpresa.descripcion"
                                    :class="{
                                        'is-invalid': errors.descripcion,
                                    }"
                                />
                                <InputError :error="errors.descripcion" />
                            </div>
                        </div>
                    </div>
                </form>
                <template #footer>
                    <button
                        class="btn btn-outline-danger"
                        @click.prevent="closeEmpresa"
                    >
                        <feather-icon icon="XIcon" />
                        <span>Cancelar</span>
                    </button>
                    <button
                        class="btn btn-success"
                        @click.prevent="storeEmpresa"
                        :disabled="formEmpresa.processing"
                    >
                        <feather-icon icon="PlusIcon" />
                        <span>Agregar</span>
                    </button>
                </template>
            </Modal>
        </section>
    </LayoutContent>
</template>
<script>
import { useForm } from "@inertiajs/vue2";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import Modal from "../../Components/Modal.vue";
import InputError from "../../Components/InputError.vue";
import Button from "../../Components/Button.vue";
import SearchEmpresa from "../../Components/SearchEmpresa.vue";
import SelectSearch from "./components/SelectSearch.vue";
import {
    alertSuccess,
    alertWarning,
    confirm,
    alertError,
} from "../../sweetAlert2.js";
import { routeTo } from "../../helpers.js";
import useVuelidate from "@vuelidate/core";
import { requiredAddJob } from "../../Validators/addJobBoardValidator.js";
import Vue from "vue";
import { toInteger } from "lodash";
import { cleanObject } from "../../utils/objectProcess.js";
import { min } from "lodash";
import SelectDepartament from "../../Components/SelectDepartament.vue";
import SelectProvince from "../../Components/SelectProvince.vue";
import SelectDistrict from "../../Components/SelectDistrict.vue";
import Spinner from "../../Components/Spinner.vue";

export default {
    components: {
        Spinner,
        SelectDistrict,
        SelectProvince,
        SelectDepartament,
        LayoutContent,
        Card,
        Modal,
        InputError,
        Button,
        SearchEmpresa,
        SelectSearch,
    },
    name: "Institute",
    props: {
        departamentos: Array,
        programa_estudios: Array,
        propuesta: Array,
        isEdit: Boolean,
    },
    data() {
        return {
            provincias: "",
            distritos: "",
            cod_dpto: "0",
            cod_prov: "0",
            cod_dpto_empresa: "0",
            cod_prov_empresa: "0",
            actualda_date: "",
            id_propuesta: 0,
            nombre_empresa: "",

            // FORMULARIOS
            formPropuesta: useForm({
                representante: "",
                direccion: "",
                correo: "",
                telefono: "",
                id_empresa: 0,
                id_programa_estudio: 0,
                pagina_web: "",
                fecha_inicio: "",
                fecha_fin: "",
                puesto: "",
                descripcion_puesto: "",
                requisitos: "",
                funciones: "",
                id_ubigeo: 0,
                modalidad_trabajo: "0",
                nivel_trabajo: "0",
                horario: "",
                salario: "",
                numero_vacantes: "",
                link_postulacion: "",
                disponibilidad_viajar: "I",
                apto_discapacitados: "A",
            }),

            formEmpresa: useForm({
                ruc: "",
                razon_social: "",
                nombre_comercial: "",
                rubro: "",
                id_ubigeo_empresa: 0,
                direccion: "",
                telefono: "",
                representante_legal: "",
                email: "",
                pagina_web: "",
                descripcion: "",
            }),
        };
    },
    methods: {
        // REDIRECCIÓN

        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${this.urlRoute}/${name}`));
        },

        // VALIDATIONS

        validarFechas() {
            const inicio = new Date(this.formPropuesta.fecha_inicio);
            const fin = new Date(this.formPropuesta.fecha_fin);

            if (fin < inicio) {
                alertWarning(
                    "La fecha de fin no puede ser menor que la fecha de inicio."
                );
                return false;
            }

            return true;
        },
        handleEmpresa(data) {
            this.formEmpresa.razon_social = data.razon_social;
            this.formEmpresa.nombre_comercial = data.nombre_comercial;
            this.formEmpresa.direccion = data.direccion;
            this.formEmpresa.telefono = data.telefono;
        },

        handleEmpresaSelect(data) {
            if (data.id === undefined) {
                return;
            }
            this.nombre_empresa =
                data.ruc + " - " + (data.razon_social || data.nombre_comercial);
            this.formPropuesta.id_empresa = data.id;
            this.formPropuesta.representante = data.representante_legal;
            this.formPropuesta.correo = data.email;
            this.formPropuesta.telefono = data.telefono;
            this.formPropuesta.pagina_web = data.pagina_web;
        },

        createEmpresa() {
            this.formEmpresa.reset();
            //this.$refs.modalEmpresaEdit.hide();
            this.$refs.modalEmpresa.show();
        },

        closeEmpresa() {
            this.formEmpresa.reset();
            this.$refs.modalEmpresa.hide();
        },

        async storeEmpresa() {
            const data = this.formEmpresa.data();

            const newData = cleanObject(data);

            console.log(newData);

            this.$http({
                method: "POST",
                url: this.routeTo("empresas/store"),
                data: newData,
                headers: {
                    "X-Inertia-Error-Bag": "empresa",
                },
            }).then((res) => {
                alertSuccess("Datos Guardados Correctamente");
                this.formEmpresa.reset();
                this.cod_dpto_empresa = "0";
                this.cod_prov_empresa = "0";
                this.$refs.modalEmpresa.hide();
                const data = res.data;
                this.$refs.empresa.setData({
                    ...data.empresa,
                    empresa:
                        data.empresa.ruc + " - " + data.empresa.razon_social,
                });
                this.formPropuesta.id_empresa = data.empresa.id;
                this.formPropuesta.representante = data.empresa.representante;
            });
            // .catch((error) => {
            //     console.error("Error al guardar horarios:", error);
            //     alertError("Error al guardar la propuesta");
            // });
        },

        // PROPUESTA
        storePropuesta() {
            const data = this.formPropuesta.data();

            const {
                correo,
                representante,
                telefono,
                pagina_web,
                numero_vacantes,
                ..._data
            } = data;

            const newData = cleanObject({
                ..._data,
                numero_vacantes: toInteger(numero_vacantes),
            });

            const datesValidate = this.validarFechas();

            if (!datesValidate) return;

            console.log(newData);
            this.formPropuesta.processing = true;
            if (!this.isEdit) {
                this.$http({
                    method: "POST",
                    url: this.routeTo(`${this.urlRoute}`),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "bolsaLaboral",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.redirect();
                    })
                    .finally(() => {
                        this.formPropuesta.processing = false;
                    });
                // .catch((error) => {
                //     console.error("Error al guardar horarios:", error);
                //     alertError("Error al guardar la propuesta");
                // });
            } else {
                console.log("Editar");
                console.log("newDataEdit", newData);

                this.$http({
                    method: "PATCH",
                    url: this.routeTo(
                        `${this.urlRoute}/update/${this.id_propuesta}`
                    ),
                    data: newData,
                    headers: {
                        "X-Inertia-Error-Bag": "bolsaLaboral",
                    },
                })
                    .then((res) => {
                        alertSuccess("Datos Guardados Correctamente");
                        this.redirect();
                    })
                    .finally(() => {
                        this.formPropuesta.processing = false;
                    });
            }
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
    computed: {
        errors() {
            return (
                this.$page.props.errors.bolsaLaboral ||
                this.$page.props.errors.empresa ||
                {}
            );
        },
        urlRoute() {
            return "bolsa_laborales";
        },
        fechaActual() {
            const today = new Date();
            return today.toISOString().split("T")[0]; // Formato 'YYYY-MM-DD'
        },
    },
    mounted() {
        if (this.isEdit) {
            this.$nextTick(() => {
                const propuestaData = this.propuesta[0];
                if (propuestaData.empresa) {
                    this.$refs.empresa.setData({
                        ...propuestaData.empresa,
                    });
                }

                this.id_propuesta = propuestaData.id_propuesta || 0;
                this.formPropuesta.fecha_inicio =
                    propuestaData.fecha_inicio || "";
                this.formPropuesta.representante =
                    propuestaData.representante_legal || "";
                this.formPropuesta.direccion = propuestaData.direccion || "";
                this.formPropuesta.correo = propuestaData.correo || "";
                this.formPropuesta.telefono = propuestaData.telefono || "";
                this.formPropuesta.pagina_web = propuestaData.pagina_web || "";
                this.formPropuesta.id_empresa = propuestaData.empresa.id || 0;
                this.nombre_empresa = propuestaData.empresa.empresa || "";
                this.formPropuesta.fecha_fin = propuestaData.fecha_fin || "";
                this.formPropuesta.puesto = propuestaData.puesto || "";
                this.formPropuesta.descripcion_puesto =
                    propuestaData.descripcion_puesto || "";
                this.formPropuesta.requisitos = propuestaData.requisitos || "";
                this.formPropuesta.funciones = propuestaData.funciones || "";
                this.formPropuesta.id_ubigeo = propuestaData.ubigeo.id || 0;
                this.formPropuesta.modalidad_trabajo =
                    propuestaData.modalidad_trabajo || "0";
                this.formPropuesta.nivel_trabajo =
                    propuestaData.nivel_trabajo || "0";
                this.formPropuesta.horario = propuestaData.horario || "";
                this.formPropuesta.salario = propuestaData.salario || "";
                this.formPropuesta.numero_vacantes =
                    propuestaData.numero_vacantes || "";
                this.formPropuesta.link_postulacion =
                    propuestaData.link_postulacion || "";
                this.formPropuesta.disponibilidad_viajar =
                    propuestaData.disponibilidad_viajar || "I";
                this.formPropuesta.apto_discapacitados =
                    propuestaData.apto_discapacitados || "A";
                this.formPropuesta.id_programa_estudio =
                    propuestaData.id_programa_estudio || 0;
                this.cod_dpto = propuestaData.ubigeo.cod_dpto;
                this.cod_prov = propuestaData.ubigeo.cod_prov;
            });
        }
    },
};
</script>
