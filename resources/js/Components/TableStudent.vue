<template>
    <div class="table-student">
        <table>
            <tr>
                <!-- <th class="checkBox">
                    <input
                        type="checkbox"
                        @change="selectAllRows"
                        v-model="allRows"
                    />
                </th> -->
                <th>CÓDIGO</th>
                <th>ALUMNO</th>
                <th>TELEFONO</th>
                <th class="action">CORREO</th>
                <th>PERFIL</th>
            </tr>
            <tr v-for="(item, index) in alumnos" :key="index">
                <td>{{ item.numero_documento }}</td>
                <td>{{ item.estudiante }}</td>
                <td>{{ item.telefono }}</td>
                <td>
                    <p>{{ item.email }}</p>
                </td>
                <td>
                    <Button
                        ref="btnEdit"
                        class="btn btn-primary"
                        @click.native.prevent="
                            perfil(item.id_estudiante, index)
                        "
                    >
                        <feather-icon icon="UserIcon" class="mr-10" />
                    </Button>
                </td>
            </tr>
        </table>
        <div class="not-length" v-if="alumnos.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">Aún no hay alumnos matriculados</span>
        </div>
        <Modal ref="modalPerfil" modal="modal-lg">
            <template #title>Perfil Estudiante</template>
            <!-- Espacio para el BODY -->
            <div class="perfil">
                <section class="perfil-img">
                    <div class="row">
                        <div class="image col-md-12 col-lg-12">
                            <img
                                class="foto"
                                :src="fotografia"
                                alt="Fotografía Estudiante"
                            />
                            <h2>
                                <strong
                                    >{{ data_perfil.nombres }}
                                    {{ data_perfil.apellido_paterno }}
                                    {{ data_perfil.apellido_materno }}</strong
                                >
                            </h2>
                        </div>
                    </div>
                </section>
                <section class="perfil-datos">
                    <div class="row-details">
                        <div class="card-details col-md-6 col-lg-6 mr-1">
                            <div class="data">
                                <p>Datos Personales</p>
                            </div>
                            <div class="data">
                                <label for="">NOMBRES:</label>
                                <p>{{ data_perfil.nombres }}</p>
                            </div>
                            <div class="data">
                                <label for="">APELLIDO PATERNO:</label>
                                <p>{{ data_perfil.apellido_paterno }}</p>
                            </div>
                            <div class="data">
                                <label for="">APELLIDO MATERNO:</label>
                                <p>{{ data_perfil.apellido_materno }}</p>
                            </div>
                            <div class="data">
                                <label for="">DNI:</label>
                                <p>{{ data_perfil.numero_documento }}</p>
                            </div>
                            <div class="data">
                                <label for="">FECHA DE NACIMIENTO:</label>
                                <p>{{ data_perfil.fecha_nacimiento }}</p>
                            </div>
                            <div class="data">
                                <label for="">GENERO:</label>
                                <p>{{ data_perfil.sexo }}</p>
                            </div>
                            <div class="data">
                                <label for="">ESTADO CIVIL:</label>
                                <p>{{ data_perfil.estado_civil }}</p>
                            </div>
                        </div>
                        <div class="card-details col-md-5 col-lg-5">
                            <div class="data">
                                <p>Contactos</p>
                            </div>
                            <div class="data">
                                <label for="">CORREO:</label>
                                <p>
                                    <feather-icon
                                        icon="MailIcon"
                                    ></feather-icon>
                                    <span>{{ data_perfil.email }}</span>
                                </p>
                            </div>
                            <div class="data">
                                <label for="">TELEFONO:</label>
                                <p>
                                    <feather-icon
                                        icon="SmartphoneIcon"
                                    ></feather-icon>
                                    <span>{{ data_perfil.telefono }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </Modal>
    </div>
</template>

<script>
import Modal from "./Modal.vue";
import Button from "./Button.vue";
import Card from "./Card.vue";

export default {
    name: "SelectTable",
    components: {
        Button,
        Modal,
    },
    props: {
        estudiantes: Object,
        items: Array,
        alumnos: Array,
        BDisable: Boolean,
        CInpunt: String,
        urlRoute: String,
    },
    data() {
        return {
            modes: ["multi", "single", "range"],
            fields: [
                "selected",
                "curso",
                "seccion",
                "creditos de Teoria",
                "Creditos de Práctica",
            ],
            fotografia: "",
            data_perfil: "",
            notas: [],
            selectMode: "multi",
            selected: [],
            allRows: "",
            note: null,
            GDisable: true,
        };
    },
    computed: {},
    methods: {
        perfil(id, index) {
            this.$refs.btnEdit[index].load();
            this.$http
                .get(this.routeTo(`gestion-cursos/data-estudiante/${id}`))
                .then((res) => {
                    this.data_perfil = res.data.data;
                    if (res.data.data.foto) {
                        this.fotografia = this.data_perfil.foto.base64;
                    }
                    this.$refs.modalPerfil.show();
                })
                .catch((error) => {
                    console.log(error);
                })
                .finally(() => {
                    this.$refs.btnEdit[index].reset();
                });
        },
    },
};
</script>
<style scoped>
.row-details {
    display: flex;
}
.card-details {
    display: flex;
    padding: 20px;
    flex-direction: column;
    border: 1px solid #e2e2e2;
    border-radius: 10px;
    height: 50%;
}
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
.table-student {
    overflow: auto;
}
.perfil {
    display: grid;
    grid-template-rows: 0.2fr 1fr;
    gap: 15px;
    margin-left: 50px;
    margin-right: 50px;
}
.data {
    width: 100%;
    display: flex;
    flex-direction: column;
    label {
        font-weight: bold;
    }
}
.row {
    .foto {
        border-radius: 50%;
        width: 20vh;
        height: 18vh;
        /* box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.06),
            0 1px 3px rgba(0, 0, 0, 0.1);*/
    }
}
.image {
    display: flex;
    align-items: center;
    gap: 15px;
    p {
        display: flex;
        align-items: center;
        margin: 0;
        gap: 5px;
    }
}
table {
    .action {
        display: flex;
        justify-content: center;
        align-items: center;
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
        padding: 30px 0 10px 10px;
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

@media (max-width: 991px) {
    .row-details {
        flex-direction: column;
        gap: 10px;
    }
    .perfil {
        margin-left: 20px;
        margin-right: 20px;
    }
    .perfil-img {
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>
