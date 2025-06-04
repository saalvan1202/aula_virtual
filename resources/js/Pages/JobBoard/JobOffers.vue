<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-3 col-lg-3">
                    <form>
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body card-body-vue">
                                    <template v-if="filtrosTags.length != 0">
                                        <div class="tags row d-flex px-1 pb-1">
                                            <div
                                                v-for="tag in filtrosTags"
                                                :key="tag.id"
                                            >
                                                <Tag
                                                    :name="tag.descripcion"
                                                    @click="
                                                        () =>
                                                            handleTag(
                                                                tag.id,
                                                                tag.default_value
                                                            )
                                                    "
                                                />
                                            </div>
                                        </div>
                                    </template>

                                    <div class="row">
                                        <div
                                            class="col-12"
                                            v-if="
                                                formFiltros.id_programa_estudio ==
                                                0
                                            "
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            handleFiltros(
                                                                e,
                                                                'id_programa_estudio',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.id_programa_estudio
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        PROGRAMA DE ESTUDIOS
                                                    </option>
                                                    <option
                                                        v-for="programa_estudio in programa_estudios"
                                                        :key="
                                                            programa_estudio.id
                                                        "
                                                        :value="
                                                            JSON.stringify({
                                                                value: programa_estudio.id,
                                                                descripcion:
                                                                    programa_estudio.descripcion,
                                                            })
                                                        "
                                                    >
                                                        {{
                                                            programa_estudio.descripcion
                                                        }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="formFiltros.departamento == 0"
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            getProvincias(
                                                                e,
                                                                'departamento',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.departamento
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        DEPARTAMENTO
                                                    </option>
                                                    <option
                                                        v-for="departamento in departamentos"
                                                        :key="departamento.id"
                                                        :value="
                                                            JSON.stringify({
                                                                value: departamento.cod_dpto,
                                                                descripcion:
                                                                    departamento.nombre,
                                                            })
                                                        "
                                                    >
                                                        {{
                                                            departamento.nombre
                                                        }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="formFiltros.provincia == 0"
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            getDistritos(
                                                                e,
                                                                'provincia',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.provincia
                                                    "
                                                    :disabled="
                                                        formFiltros.departamento ==
                                                        '0'
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        PROVINCIA
                                                    </option>
                                                    <option
                                                        v-for="provincia in provincias"
                                                        :key="provincia.id"
                                                        :value="
                                                            JSON.stringify({
                                                                value: provincia.cod_prov,
                                                                descripcion:
                                                                    provincia.nombre,
                                                            })
                                                        "
                                                    >
                                                        {{ provincia.nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="formFiltros.modalidad == 0"
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            handleFiltros(
                                                                e,
                                                                'modalidad',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.modalidad
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        MODALIDAD
                                                    </option>
                                                    <option>PRESENCIAL</option>
                                                    <option>REMOTO</option>
                                                    <option>HÍBRIDO</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="
                                                formFiltros.nivel_trabajo == 0
                                            "
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            handleFiltros(
                                                                e,
                                                                'nivel_trabajo',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.nivel_trabajo
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        NIVEL DE TRABAJO
                                                    </option>
                                                    <option>
                                                        SIN EXPERIENCIA
                                                    </option>
                                                    <option>TRAINEE</option>
                                                    <option>JUNIOR</option>
                                                    <option>SEMI-SENIOR</option>
                                                    <option>SENIOR</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="formFiltros.horario == 0"
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            handleFiltros(
                                                                e,
                                                                'horario',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.horario
                                                    "
                                                >
                                                    <option value="0">
                                                        HORARIO
                                                    </option>
                                                    <option>PART-TIME</option>
                                                    <option>FULL-TIME</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="col-12"
                                            v-if="
                                                formFiltros.apto_discapacitados ==
                                                0
                                            "
                                        >
                                            <div class="form-group">
                                                <select
                                                    class="form-control"
                                                    @change="
                                                        (e) =>
                                                            handleFiltros(
                                                                e,
                                                                'apto_discapacitados',
                                                                '0'
                                                            )
                                                    "
                                                    v-model="
                                                        formFiltros.apto_discapacitados
                                                    "
                                                >
                                                    <option value="0" disabled>
                                                        DISCAPACIDAD
                                                    </option>
                                                    <option
                                                        :value="
                                                            JSON.stringify({
                                                                value: 'A',
                                                                descripcion:
                                                                    'CON DISCAPACIDAD',
                                                            })
                                                        "
                                                    >
                                                        CON DISCAPACIDAD
                                                    </option>
                                                    <option
                                                        :value="
                                                            JSON.stringify({
                                                                value: 'I',
                                                                descripcion:
                                                                    'SIN DISCAPACIDAD',
                                                            })
                                                        "
                                                    >
                                                        SIN DISCAPACIDAD
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div
                                            class="trash col-12 d-flex justify-content-center align-items-center mt-1"
                                            v-if="filtrosTags.length != 0"
                                            @click="cleanFiltros"
                                        >
                                            <feather-icon
                                                icon="TrashIcon"
                                                size="22"
                                                stroke-width="2.5"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-9 col-lg-9">
                    <Card>
                        <template v-if="ofertas.data.length != 0">
                            <div
                                v-for="oferta in ofertas.data"
                                :key="oferta.uuid"
                            >
                                <CardJobOffer
                                    :job="oferta"
                                    @click="redirect(oferta.uuid)"
                                />
                            </div>
                        </template>
                        <template v-else>
                            <div
                                class="d-flex flex-column justify-content-center align-items-center mt-1"
                            >
                                <div>
                                    <feather-icon
                                        icon="InboxIcon"
                                        class="icon-large"
                                    />
                                </div>
                                <p>No hay ofertas disponibles</p>
                            </div>
                        </template>
                        <template #header>
                            <h4 class="card-title">Ofertas laborales</h4>
                            <p class="card-text">
                                <span>{{ ofertas.total }}</span> ofertas de
                                empleo
                            </p>
                        </template>
                    </Card>
                </div>
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
import CardJobOffer from "./components/CardJobOffer.vue";
import { alertSuccess, alertWarning, confirm } from "../../sweetAlert2.js";
import { firstId } from "../../helpers.js";
import { cleanObject } from "../../utils/objectProcess.js";
import Tag from "./components/Tag.vue";
export default {
    components: {
        LayoutContent,
        Card,
        DataTable,
        Modal,
        InputError,
        Button,
        CardJobOffer,
        Tag,
    },
    name: "StudyPlan",
    props: {
        urlRoute: String,
        departamentos: Array,
        ofertas: Object,
        programa_estudios: Array,
    },
    data() {
        return {
            formFiltros: useForm({
                modalidad: "0",
                nivel_trabajo: "0",
                horario: "0",
                apto_discapacitados: "0",
                id_programa_estudio: "0",
                departamento: "0",
                provincia: "0",
            }),

            filtrosTags: [],
            provincias: [],
            distritos: [],
        };
    },
    mounted() {},
    methods: {
        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${this.urlRoute}/${name}`));
            console.log(`${this.urlRoute}/${name}`);
        },

        handleTag(id, defaultValue) {
            const data = this.formFiltros.data();
            data[id] = defaultValue;

            const newData = cleanObject(data);
            console.log(newData);

            const postData = async () => {
                try {
                    await this.$inertia.post(this.urlRoute, newData, {
                        preserveState: true,
                        only: ["ofertas"],
                    });

                    const newFiltrosTags = this.filtrosTags.filter(
                        (tag) => tag.id != id
                    );
                    this.filtrosTags = newFiltrosTags;
                    this.formFiltros[id] = defaultValue;
                } catch (error) {
                    console.error("Error during post request:", error);
                }
            };

            postData();
        },

        handleFiltros(e, param, default_value) {
            const { value } = e.target;

            let newValue;
            let newDescripcion;

            try {
                newValue = JSON.parse(value).value;
                newDescripcion = JSON.parse(value).descripcion;
            } catch (error) {
                newValue = value;
                newDescripcion = value;
            }

            this.formFiltros[param] = newValue;

            this.filtrosTags.push({
                id: param,
                descripcion: newDescripcion,
                default_value: default_value,
            });

            const data = this.formFiltros.data();

            const newData = cleanObject(data);

            this.$inertia.post(this.urlRoute, newData, {
                preserveState: true,
                only: ["ofertas"],
            });
        },

        cleanFiltros() {
            this.filtrosTags = [];

            this.formFiltros.reset();

            const data = this.formFiltros.data();

            this.$inertia.post(this.urlRoute, data, {
                preserveState: true,
                only: ["ofertas"],
            });
        },

        getProvincias(e, param, default_value) {
            this.handleFiltros(e, param, default_value);

            this.$http
                .get(
                    this.routeTo(
                        `ubigeos/provincias?cod_dpto=${this.formFiltros.departamento}`
                    )
                )
                .then((res) => {
                    this.provincias = res.data;
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
                })
                .finally(() => {});
        },

        getDistritos(e, param, default_value) {
            this.handleFiltros(e, param, default_value);

            this.$http
                .get(
                    this.routeTo(
                        `ubigeos/distritos?cod_dpto=${this.formFiltros.departamento}&cod_prov=${this.formFiltros.provincia}`
                    )
                )
                .then((res) => {
                    this.distritos = res.data;
                })
                .catch((error) => {
                    if (this.errors.record != undefined) {
                        alertWarning(this.errors.record);
                    }
                })
                .finally(() => {});
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
.card-text {
    font-size: small;
    font-weight: 600;
}

.card-text span {
    color: #7100c9;
}

.tags {
    gap: 4px;
}

.trash {
    color: #7100c9;
    cursor: pointer;
}

.icon-large {
    width: 48px; /* Ajusta el tamaño según tus necesidades */
    height: 48px; /* Ajusta el tamaño según tus necesidades */
}
</style>
