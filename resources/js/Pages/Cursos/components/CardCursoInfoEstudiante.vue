<template>
    <div class="col-md-4 col-lg-4">
        <Card>
            <template #header>
                <div class="datos-header">
                    <h3>Información General</h3>
                </div>
            </template>
            <div class="datos">
                <div>
                    <section class="datos-curso">
                        <p><strong>Unidad Didáctica:</strong></p>
                        <p :variant="bg_badge" style="width: auto">
                            {{ curso.descripcion }}
                        </p>
                    </section>
                    <section class="datos-curso">
                        <p><strong>Semestre:</strong></p>
                        <p :variant="bg_badge">{{ curso.periodo }}</p>
                    </section>
                    <section class="datos-curso">
                        <p><strong>Docente:</strong></p>
                        <p :variant="bg_badge">{{ curso.docente }}</p>
                    </section>
                    <section class="datos-curso">
                        <p><strong>Ciclo:</strong></p>
                        <p :variant="bg_badge">{{ curso.ciclo }}</p>
                    </section>
                    <section class="datos-curso">
                        <p><strong>Turno:</strong></p>
                        <p :variant="bg_badge">{{ curso.turno }}</p>
                    </section>
                </div>
                <div>
                    <div class="silabo">
                        <Button
                            block
                            ref="btnSilabo"
                            class="btn btn-success"
                            @click.native.prevent="silabo"
                        >
                            <feather-icon icon="FileTextIcon" class="mr-50" />
                            <span class="align-small">Sílabo</span>
                        </Button>
                    </div>
                </div>
            </div>
        </Card>
        <Modal ref="modalSilabo" modal="modal-lg">
            <template #title>Silabo</template>
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
        </Modal>
    </div>
</template>
<script>
import Button from "../../../Components/Button.vue";
import Card from "../../../Components/Card.vue";
import { BBadge } from "bootstrap-vue";
import Modal from "../../../Components/Modal.vue";
export default {
    name: "CardCursoInfoEstudiante",
    data() {
        return {
            bg_badge: "light-primary",
            preViewPdf: "",
        };
    },
    components: {
        Button,
        Card,
        BBadge,
        Modal,
    },
    props: {
        curso: Object,
        path: String,
    },
    methods: {
        silabo() {
            this.preViewPdf = "";
            this.$refs.btnSilabo.load();
            this.$http
                .get(this.routeTo(`gestion-cursos/silabo/${this.curso.id}`))
                .then((res) => {
                    console.log(res.data);
                    if (res.data != false) {
                        this.preViewPdf = `data:application/pdf;base64,${res.data.silabo}`;
                    }
                    this.$refs.modalSilabo.show();
                })
                .catch((error) => {
                    //console.log(error);
                })
                .finally(() => {
                    this.$refs.btnSilabo.reset();
                });
        },
        remember() {
            window.history.back();
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
    flex-direction: column;
    gap: 30px;
}
.datos-curso {
    display: flex;
    flex-direction: column;
    height: auto;
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
