<template>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <Card>
                <template #header>
                    <div class="datos-header">
                        <h3>Información General</h3>
                        <div class="silabo">
                            <Button
                                ref="btnSilabo"
                                class="btn btn-success"
                                @click.native.prevent="silabo"
                            >
                                <feather-icon
                                    icon="FileTextIcon"
                                    class="mr-50"
                                />
                                <span class="align-small">Sílabo</span>
                            </Button>
                            <Button
                                ref="btnEdit"
                                class="btn btn-dark"
                                @click.native.prevent="openConfiguracion"
                            >
                                <feather-icon icon="ToolIcon" class="mr-50" />
                                <span class="align-small">Configuración</span>
                            </Button>
                        </div>
                    </div>
                </template>
                <div>
                    <div class="datos">
                        <div>
                            <section class="datos-curso">
                                <p><strong>Unidad Didáctica:</strong></p>
                                <BBadge :variant="bg_badge">
                                    {{ curso.descripcion }}
                                </BBadge>
                            </section>
                            <section class="datos-curso">
                                <p><strong>Semestre:</strong></p>
                                <BBadge :variant="bg_badge">{{
                                    curso.periodo
                                }}</BBadge>
                            </section>
                            <section class="datos-curso">
                                <p><strong>Docente:</strong></p>
                                <BBadge :variant="bg_badge">{{
                                    curso.docente
                                }}</BBadge>
                            </section>
                        </div>
                        <div>
                            <section class="datos-curso">
                                <p><strong>Ciclo:</strong></p>
                                <BBadge :variant="bg_badge">{{
                                    curso.ciclo
                                }}</BBadge>
                            </section>
                            <section class="datos-curso">
                                <p><strong>Turno:</strong></p>
                                <BBadge :variant="bg_badge">{{
                                    curso.turno
                                }}</BBadge>
                            </section>
                            <section class="datos-curso">
                                <p><strong>Estado:</strong></p>
                                <BBadge
                                    variant="light-danger"
                                    v-if="curso.culminado == 'S'"
                                    >CULMINADO</BBadge
                                >
                                <BBadge variant="light-success" v-else
                                    >EN PROCESO</BBadge
                                >
                            </section>
                        </div>
                    </div>
                    <div>
                        <div class="progres-curso">
                            <p style="padding-top: 8px !important">
                                <strong>
                                    {{
                                        `Clases: ${curso.total_desarrolladas} de ${curso.total_clases}`
                                    }}
                                </strong>
                            </p>
                            <BProgress
                                variant="primary"
                                :max="curso.total_clases"
                            >
                                <BProgressBar
                                    :value="curso.total_desarrolladas"
                                    :label="`${(
                                        (curso.total_desarrolladas /
                                            curso.total_clases) *
                                        100
                                    ).toFixed(2)}%`"
                                ></BProgressBar>
                            </BProgress>
                        </div>
                    </div>
                </div>
            </Card>
        </div>
        <Modal ref="modalSilabo" modal="modal-lg">
            <template #title>Silabo</template>
            <div
                style="
                    width: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                "
            >
                <input
                    accept=".pdf"
                    style="display: none"
                    type="file"
                    name=""
                    id="inputSilabo"
                    @change="updateFileName"
                />
            </div>
            <object
                v-if="preViewPdf"
                :data="preViewPdf"
                type="application/pdf"
                width="100%"
                height="600"
            ></object>
            <div class="not-length" v-else>
                <feather-icon icon="InboxIcon" />
                <span class="align-small">Sílabo no disponible</span>
            </div>
            <section
                style="
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                "
            >
                <Button
                    class="btn btn-success mb-1"
                    id="buttonSilabo"
                    @click.native.prevent="inputSelect"
                    v-if="estadoCurso != 'S'"
                >
                    <feather-icon icon="UploadIcon"></feather-icon>
                    <span>Subir</span>
                </Button>
                <p>{{ fileName }}</p>
            </section>
            <template #footer>
                <button class="btn btn-outline-danger" @click.prevent="close">
                    <feather-icon icon="XIcon" />
                    <span>Cancelar</span>
                </button>
                <Button
                    ref="btnSave"
                    class="btn btn-success"
                    @click.native.prevent="store"
                    :disabled="form.processing"
                    v-if="estadoCurso != 'S'"
                >
                    <feather-icon icon="SaveIcon" />
                    <span>Guardar</span>
                </Button>
            </template>
        </Modal>
        <Modal ref="modalConfiguracion" modal="modal-lg">
            <template #title>Configuración</template>
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <b-card
                        border-variant="danger"
                        header="Culminar Unidad Didáctica"
                        header-bg-variant="danger"
                        header-text-variant="white"
                    >
                        <section>
                            <b-card-text style="margin-top: 10px"
                                >Verifique que todas las actividades
                                (SESIONES,EVALUACIONES Y ASISTENCIAS) estén
                                completas.</b-card-text
                            >
                            <b-card-text style="color: #ea5455"
                                >Una vez culminada, ya no se podrá realizar
                                modificaciones.</b-card-text
                            >
                        </section>
                        <section
                            style="
                                display: flex;
                                justify-content: end;
                                margin-right: 10px;
                                margin-top: 10px;
                            "
                        >
                            <Button
                                class="btn btn-danger"
                                ref="btnCulminar"
                                @click.native.prevent="culminar"
                            >
                                <feather-icon icon="ToggleRightIcon" />
                                <span>Culminar</span>
                            </Button>
                        </section>
                    </b-card>
                </div>
            </div>
        </Modal>
    </div>
</template>
<script>
import Button from "../../../Components/Button.vue";
import Card from "../../../Components/Card.vue";
import { BBadge, BProgress, BProgressBar } from "bootstrap-vue";
import Modal from "../../../Components/Modal.vue";
import { update } from "lodash";
import { useForm } from "@inertiajs/vue2";
import { objectToFormData } from "../../../formData";
import { alertSuccess, alertWarning } from "../../../sweetAlert2";
export default {
    name: "CardCursoInfo",
    data() {
        return {
            pdf: "/public/images/cv.pdf",
            bg_badge: "light-primary",
            preViewPdf: "",
            estadoCurso: "",
            fileName: "",
            form: useForm({
                silabo: "",
                uuid: "",
                id: "",
            }),
        };
    },
    components: {
        Button,
        Card,
        BBadge,
        BProgress,
        BProgressBar,
        Modal,
    },
    props: {
        curso: Object,
        path: String,
        uuid: String,
    },
    mounted() {
        console.log(this.curso);
    },
    methods: {
        culminar() {
            this.$refs.btnCulminar.load();
            this.$http({
                method: "POST",
                url: this.routeTo(`gestion-cursos/culminar-curso/${this.uuid}`),
                data: objectToFormData(this.form.data()),
            })
                .then((res) => {
                    if (res.data.state) {
                        alertWarning(res.data.error);
                    } else {
                        alertSuccess("Culminado Correctamente");
                        console.log(res.data);
                    }
                })
                .catch((error) => {})
                .finally(() => {
                    this.$refs.btnCulminar.reset();
                });
        },
        openConfiguracion() {
            this.$refs.modalConfiguracion.show();
        },
        close() {
            this.$refs.modalSilabo.hide();
        },
        openSilbo() {
            this.$refs.modalSilabo.show();
        },
        remember() {
            window.history.back();
        },

        inputSelect() {
            const input = document.getElementById("inputSilabo");
            input.click();
        },
        updateFileName() {
            const input = document.getElementById("inputSilabo");
            this.fileName = input.files[0].name;
            this.form.silabo = input.files[0];
            if (input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.preViewPdf = e.target.result;
                    console.log("ok", this.preViewPdf);
                };
                reader.readAsDataURL(input.files[0]);
            }
        },
        silabo() {
            this.fileName = "";
            this.preViewPdf = "";
            this.form.reset();
            this.$refs.btnSilabo.load();
            this.$http
                .get(this.routeTo(`gestion-cursos/silabo/${this.curso.id}`))
                .then((res) => {
                    if (res.data != false) {
                        this.estadoCurso = res.data.data;
                        if (res.data.silabo != false) {
                            this.preViewPdf = `data:application/pdf;base64,${res.data.silabo}`;
                        }
                    }
                    console.log(this.preViewPdf);
                    this.$refs.modalSilabo.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnSilabo.reset();
                });
        },
        store() {
            this.form.uuid = this.curso.uuid;
            this.form.id = this.curso.id;
            this.$refs.btnSave.load();
            this.$http({
                method: "POST",
                url: this.routeTo(`gestion-cursos/silabo`),
                data: objectToFormData(this.form.data()),
            })
                .then((res) => {
                    alertSuccess("Sílabo Guardado Correctamente");
                })
                .catch((error) => {})
                .finally(() => {
                    this.$refs.btnSave.reset();
                    this.form.processing = false;
                });
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
    transition: all 0.1s ease-in-out, background 0s, color 0s, border-color 0s;
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
    height: 3vh;
    gap: 5px;
    margin-bottom: 5px;
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
.progres-curso {
    width: 47%;
}
@media (max-width: 710px) {
    .datos {
        flex-direction: column;
        gap: 5px;
    }
    .datos-curso {
        height: auto;
        flex-direction: column;
        p {
            margin-bottom: 0;
        }
    }
    .datos-header {
        display: flex;
        width: 100%;
        flex-direction: column;
        align-items: center;
    }
    .progres-curso {
        width: 100%;
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
