<template>
    <div>
        <Card>
            <template #header>
                <div
                    style="
                        display: flex;
                        justify-content: space-between;
                        width: 100%;
                    "
                >
                    <section>
                        <h4 class="card-title">Configuración</h4>
                        <h7 class="mt-3">
                            <strong>
                                {{ criterio.nombre }} - PORCENTAJE:
                                {{ parseInt(criterio.porcentaje) }}%
                            </strong>
                        </h7>
                    </section>
                    <section class="button-conf">
                        <Button
                            ref="btnDestroy"
                            class="btn btn-success btn-sm"
                            @click.native.prevent="ponderar()"
                        >
                            <feather-icon icon="DivideIcon" class="mr-20" />
                            <span class="align-small">Ponderar</span>
                        </Button>
                        <Button
                            title="Agregar Actividad"
                            ref="btnDestroy"
                            class="btn btn-primary btn-sm"
                            @click.native.prevent="create()"
                        >
                            <feather-icon icon="PlusIcon" class="mr-20" />
                            <span class="align-small">Agregar</span>
                        </Button>
                    </section>
                </div>
            </template>
            <div class="table-evaluacion">
                <table>
                    <tr>
                        <th style="text-align: center">ACTIVIDAD</th>
                        <th style="text-align: center">PESO</th>
                        <th style="text-align: center">FECHA</th>
                        <th style="text-align: center">OPCIONES</th>
                    </tr>
                    <tbody>
                        <tr
                            v-for="(item, index) in actividadesC"
                            :key="item.id"
                        >
                            <td>{{ item.nombre }}</td>
                            <td style="width: 15%">
                                <BInputGroup append="%">
                                    <BFormInput
                                        class="form-control"
                                        type="number"
                                        v-model="pesos[index]"
                                    >
                                    </BFormInput>
                                </BInputGroup>
                            </td>
                            <td style="text-align: center">
                                {{ item.fecha_inicio }}
                            </td>
                            <td style="text-align: center">
                                <Button
                                    title="Eliminar Actvidad"
                                    ref="btnDestroy"
                                    class="btn btn-danger btn-sm"
                                    @click.native.prevent="
                                        deleteActividades(item.id, index)
                                    "
                                >
                                    <feather-icon
                                        icon="Trash2Icon"
                                        class="mr-20"
                                    />
                                </Button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="not-length" v-if="actividadesC.length == 0">
                    <feather-icon icon="InboxIcon" />
                    <span class="align-small">No se agragaron actividades</span>
                </div>
            </div>
            <div style="display: flex; justify-content: center; gap: 10px">
                <Button
                    title="Previsualizar"
                    ref="btnPrevisualizar"
                    class="btn btn-success btn-sm"
                    @click.native.prevent="store()"
                >
                    <feather-icon icon="EyeIcon" class="mr-20" />
                    <span class="align-small">Previsualizar</span>
                </Button>
                <Button
                    title="Publicar"
                    ref="btnPublicar"
                    class="btn btn-primary btn-sm"
                    @click.native.prevent="subitNotas()"
                >
                    <feather-icon icon="ArrowUpIcon" class="mr-20" />
                    <span class="align-small">Publicar</span>
                </Button>
            </div>
        </Card>
        <Modal ref="modalActividades" modal="modal-lg">
            <template #title>Actividades</template>
            <!-- BODY  pills-->
            <BTabs nav-wrapper-class="custom-tabs" align="center">
                <BTab @click="resetActividades('examenes')">
                    <template #title>
                        <div class="title-actividades">
                            <feather-icon icon="EditIcon" />
                            <span>Exámenes</span>
                        </div>
                    </template>

                    <TableActividades
                        :actividadesC="examenes"
                        :actividadesAgregadas="actividadesC"
                        @update-actividades="updateActividades"
                    />
                </BTab>
                <BTab @click="resetActividades('tareas')">
                    <template #title>
                        <div class="title-actividades">
                            <feather-icon icon="BookOpenIcon" />
                            <span>Tareas</span>
                        </div>
                    </template>

                    <TableActividades
                        :actividadesC="tareas"
                        :actividadesAgregadas="actividadesC"
                        @update-actividades="updateActividades"
                    />
                </BTab>
                <BTab @click="resetActividades('foros')">
                    <template #title>
                        <div class="title-actividades">
                            <feather-icon icon="BookOpenIcon" />
                            <span>Foros</span>
                        </div>
                    </template>

                    <TableActividades
                        :actividadesC="foros"
                        :actividadesAgregadas="actividadesC"
                        @update-actividades="updateActividades"
                    />
                </BTab>
            </BTabs>
        </Modal>
    </div>
</template>
<script>
import { BCardText, BFormInput, BInputGroup, BTab, BTabs } from "bootstrap-vue";
import Card from "../../../Components/Card.vue";
import Modal from "../../../Components/Modal.vue";
import TableActividades from "./TableActividades.vue";
import { alertWarning, confirm } from "../../../sweetAlert2";
import { useForm } from "@inertiajs/vue2";
import Button from "../../../Components/Button.vue";
export default {
    name: "TableConfiguracionNotas",
    props: {
        evaluaciones: Array,
        tareas: Array,
        foros: Array,
        criterio: Object,
        urlRoute: String,
        uuid: String,
        storeF: Function,
        globalStateV: Boolean,
    },
    components: {
        BFormInput,
        BInputGroup,
        Card,
        Modal,
        BTab,
        BTabs,
        BCardText,
        TableActividades,
        Button,
    },
    data() {
        return {
            pesoNota: "",
            actividadesC: [],
            examenes: [],
            pesos: [],
            estadoPreView: false,
            form: useForm({
                tipoActividad: "",
                uuid: "",
                id_criterio_evaluacion: "",
                configuracion: [],
            }),
        };
    },
    mounted() {
        this.estadoPreView = this.globalStateV;
        this.form.uuid = this.uuid;
        this.form.id_criterio_evaluacion = this.criterio.id;
    },
    methods: {
        async subitNotas() {
            this.estadoPreView = this.globalStateV;
            if (this.estadoPreView) {
                this.$refs.btnPublicar.load();
                await this.storeF();
                this.$refs.btnPublicar.reset();
            } else {
                alertWarning("No puedes subir notas sin antes previsualizar");
            }
        },
        resetActividades(tipoActividad) {
            this.form.tipoActividad = tipoActividad;
            console.log(this.form.tipoActividad);
            this.actividadesC = [];
        },
        setPreNotas(notasFinal) {
            this.$emit("set-pre-notas", notasFinal);
        },
        close() {
            this.$refs.modalActividades.hide();
        },
        create() {
            this.$refs.modalActividades.show();
        },
        ponderar() {
            this.pesoNota = 100 / this.actividadesC.length;
            this.pesos = this.pesos.map(() => this.pesoNota);
            console.log(this.pesos[0]);
        },
        //STORE PARA PREVISUALIZAR LAS NOTAS
        store() {
            let sumPesos = 0;
            this.pesos.map((item) =>
                console.log((sumPesos += parseFloat(item)))
            );
            if (sumPesos != 100) {
                alertWarning(
                    "Para poder previsualizar las notas la suma total de los porcentajes debe que ser del 100%"
                );
                return;
            }
            this.actividadesC.map((item, index) => {
                item.peso = this.pesos[index];
            });
            this.estadoPreView = true;
            this.$emit("notas-aula", this.estadoPreView);
            this.$refs.btnPrevisualizar.load();

            this.form.configuracion = this.actividadesC;
            this.$http({
                method: "POST",
                url: this.routeTo(`calificaciones/pre-visualizar`),
                data: this.form.data(),
            })
                .then((res) => {
                    this.setPreNotas(res.data);
                    console.log(res.data);
                    alertSuccess("Notas Guardadas Correctamente");
                    this.$refs.datatable.reload();
                    this.$refs.modal.hide();
                })
                .catch((error) => {})
                .finally(() => {
                    this.form.processing = false;
                    this.$refs.btnPrevisualizar.reset();
                });
        },
        updateActividades(data) {
            this.actividadesC = [...data];
            this.pesos = this.actividadesC.map(() => "");
            this.$emit("actividades", this.actividadesC);
            console.log(this.pesos);
        },
        deleteActividades(id, index) {
            confirm(
                {
                    text: "Desea eliminar el registro seleccionado?",
                },
                () => {
                    const index = this.actividadesC.findIndex(
                        (item) => item.id == id
                    );
                    this.actividadesC.splice(index, 1);
                    this.pesos.splice(index, 1);
                }
            );
        },
    },
};
</script>
<style scoped>
.not-length {
    height: 50px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.not-length .feather {
    height: 4rem !important;
}

.title-actividades {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}
.title-actividades .feather {
    height: 2rem;
}
.table-evaluacion {
    display: flex;
    flex-direction: column;
    gap: 20px;
    margin-bottom: 20px;
    justify-content: center;
    align-items: center;
}
table {
    .indicador {
        background-color: #c9c9c9;
        width: 100%;
        font-weight: bold;
    }
    .action {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh;
        padding-left: 0px;
    }

    width: 100%;
    th {
        height: 6vh;
        padding-left: 10px;
        min-width: 8vw;
        background-color: #f3f2f7;
    }
    td {
        padding: 8px 0 10px 10px;
    }
    .inputD {
        height: 6vh;
        width: 10vh;
        text-align: center;
    }
    .inputE {
        height: 6vh;
        width: 10vh;
        text-align: center;
        outline: none;
    }
}
.button-conf {
    display: flex;
    height: 5vh;
    gap: 5px;
}
@media (max-width: 500px) {
    .button-conf {
        flex-direction: column;
        gap: 5px;
        margin-bottom: 20px;
    }
}
</style>
