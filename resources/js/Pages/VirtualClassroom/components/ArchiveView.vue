<template>
    <div>
        <div
            :class="
                secondary
                    ? 'archivo-enviado secondary'
                    : primary
                    ? 'archivo-enviado primary'
                    : 'archivo-enviado'
            "
            @click="descargarArchivo(id, archivo, nombre)"
        >
            <div class="archivo-enviado__content">
                <div
                    :class="
                        obtenerExtension(archivo) == 'pdf'
                            ? 'archivo-enviado__icon pdf'
                            : obtenerExtension(archivo) == 'docx' ||
                              obtenerExtension(archivo) == 'doc'
                            ? 'archivo-enviado__icon docx'
                            : obtenerExtension(archivo) == 'xls' ||
                              obtenerExtension(archivo) == 'xlsx'
                            ? 'archivo-enviado__icon xlsx'
                            : obtenerExtension(archivo) == 'txt'
                            ? 'archivo-enviado__icon txt'
                            : obtenerExtension(archivo) == 'zip'
                            ? 'archivo-enviado__icon zip'
                            : ''
                    "
                >
                    <span>
                        <feather-icon
                            size="20"
                            :icon="
                                obtenerExtension(archivo) == 'pdf'
                                    ? 'FilePlusIcon'
                                    : obtenerExtension(archivo) == 'docx' ||
                                      obtenerExtension(archivo) == 'doc'
                                    ? 'FileMinusIcon'
                                    : obtenerExtension(archivo) == 'xlsx' ||
                                      obtenerExtension(archivo) == 'xls'
                                    ? 'FileIcon'
                                    : obtenerExtension(archivo) == 'txt'
                                    ? 'FileTextIcon'
                                    : obtenerExtension(archivo) == 'zip'
                                    ? 'ArchiveIcon'
                                    : ''
                            "
                        ></feather-icon>
                    </span>
                </div>
                <p class="m-0 p-0">
                    {{ nombre }}.{{ obtenerExtension(archivo) }}
                </p>
            </div>
            <div v-if="primary" class="ultimo-enviado">
                <p class="m-0 p-0">{{ primary_text }}</p>
            </div>
        </div>
        <Modal ref="modalPre" modal="modal-lg">
            <template #title>{{ estudiante }}</template>
            <object
                :data="urlData"
                type="application/pdf"
                sandbox="allow-scripts allow-same-origin allow-forms allow-top-navigation-by-user-activation"
                width="100%"
                height="600"
            ></object>
        </Modal>
    </div>
</template>
<script>
import Modal from "../../../Components/Modal.vue";
export default {
    name: "ArchiveView",
    data() {
        return {
            urlData: "",
        };
    },
    components: {
        Modal,
    },
    props: {
        estudiante: {
            type: String,
            required: true,
        },
        id: {
            type: Number,
            required: true,
        },
        archivo: {
            type: String,
            required: true,
        },
        nombre: {
            type: String,
            required: true,
        },
        secondary: {
            type: Boolean,
            default: false,
        },
        primary: {
            type: Boolean,
            default: false,
        },
        primary_text: {
            type: String,
            default: "Último enviado",
        },
    },
    methods: {
        hide() {
            this.$refs.modalPre.hide();
        },
        obtenerExtension(nombreArchivo) {
            const extension = nombreArchivo.split(".").pop(); // Divide el nombre del archivo por el punto y obtiene la última parte
            return extension;
        },
        descargarArchivo(id, archivo, nombre) {
            if (this.obtenerExtension(archivo) == "pdf") {
                let url = this.routeTo(`archivos/mostrar/${id}/${archivo}`);
                window.open(url, "_blank");
                return;
            }

            //let document = this.routeTo(`archivos/descargar/${id}/${archivo}`);
            this.urlData = `https://view.officeapps.live.com/op/embed.aspx?src=${this.routeTo(
                `archivos/mostrar/${id}/${archivo}`
            )}`;
            this.$refs.modalPre.show();
        },
    },
};
</script>
<style scoped>
.archivo-enviado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #bdbdbd;
    border-radius: 5px;
    padding: 6px;

    .archivo-enviado__content {
        display: flex;
        align-items: center;
        gap: 5px;

        p {
            font-size: 12px;
        }

        .archivo-enviado__icon {
            padding: 4px;
            border-radius: 4px;

            span {
                color: white;
            }
        }

        .pdf {
            background-color: #e30809;
            border: 1px solid #c10000;
        }

        .docx {
            background-color: #2a79cd;
            border: 1px solid #233f6c;
        }

        .docx {
            background-color: #2a79cd;
            border: 1px solid #233f6c;
        }

        .xlsx {
            background-color: #1a9d49;
            border: 1px solid #0f6c2f;
        }

        .txt {
            background-color: #aeaeae;
            border: 1px solid #949494;
        }

        .zip {
            background-color: #f3b23b;
            border: 1px solid #d99c00;
        }
    }
}

.archivo-enviado:hover {
    background-color: #f3f3f3;
    cursor: pointer;
}

.secondary {
    background-color: #ececec;
}

.primary:hover {
    background-color: #eff4f9;
    border: 1px solid #0f6cbf;
}

.ultimo-enviado {
    font-size: 10px;
    padding-right: 5px;
    font-weight: bold;
}
</style>
