<template>
    <LayoutContent>
        <ReturnButton title="Volver" :path_remove="`foros/${foro.id}`" />
        <Card>
            <template #header>
                <h3 class="m-0 p-0">Foro</h3>
            </template>
            <template class="foro">
                <div class="foro-container">
                    <section>
                        <p class="m-0 p-0 foro-container__title">
                            <strong>Titulo:</strong>
                            {{ capitalizeWord(foro.seccion_recurso.nombre) }}
                        </p>
                    </section>
                    <section>
                        <p class="m-0 p-0 foro-container__theme">
                            {{
                                capitalizeWord(foro.seccion_recurso.descripcion)
                            }}
                        </p>
                    </section>
                </div>
                <div class="foro-respuestas">
                    <p class="m-0 p-0 foro-container__title">
                        <strong>Respuestas:</strong>
                    </p>
                    <div
                        v-if="comentarios.length == 0"
                        class="empty d-flex flex-column justify-content-center align-items-center"
                    >
                        <div>
                            <feather-icon icon="InboxIcon" size="30" />
                        </div>
                        <p>¡Se el primero en comentar!</p>
                    </div>
                    <div
                        v-else
                        class="comentario"
                        v-for="(item, index) in comentarios"
                        :key="item.id"
                        style="cursor: pointer"
                    >
                        <div class="comentario-icon">
                            <div class="comentario-icon__style">
                                <span>
                                    <feather-icon
                                        size="15"
                                        icon="UserIcon"
                                    ></feather-icon>
                                </span>
                            </div>
                        </div>
                        <div class="comentario-content">
                            <p class="m-0 p-0 comentario-content__name">
                                <strong
                                    >{{
                                        capitalizeWord(item.estudiante.nombres)
                                    }}
                                    {{
                                        capitalizeWord(
                                            item.estudiante.apellido_paterno
                                        )
                                    }}</strong
                                >
                            </p>
                            <div>
                                <p
                                    class="m-0 p-0 comentario-content__respuesta"
                                    v-html="
                                        formattedDescripcion(
                                            item.comentario.descripcion
                                        )
                                    "
                                ></p>
                            </div>
                        </div>
                        <div class="comentario-options">
                            <div class="comentario-options__icons">
                                <div class="icon-options">
                                    <span
                                        @click="
                                            destroyComentario(
                                                item.comentario.id
                                            )
                                        "
                                        class="icon-d"
                                    >
                                        <feather-icon
                                            size="13"
                                            icon="TrashIcon"
                                        ></feather-icon>
                                    </span>
                                    <span class="tooltip-options tooltip-delete"
                                        >Eliminar</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="index != comentarios.length - 1"
                            class="line"
                        ></div>
                    </div>
                </div>
            </template>
        </Card>
    </LayoutContent>
</template>

<script>
import Card from "../../Components/Card.vue";
import LayoutContent from "../../Layouts/LayoutContent.vue";
import ReturnButton from "../Cursos/components/ReturnButton.vue";
import { alertSuccess, confirm } from "../../sweetAlert2";
import { capitalizeFirstWord } from "../../utils/textProcess";

export default {
    components: {
        LayoutContent,
        Card,
        ReturnButton,
    },
    name: "Forum",
    props: {
        urlRoute: String,
        foro: Object,
        comentarios: Array,
    },
    data() {
        return {
            inputValue: "", // Valor del input
            detectedUrl: null, // URL detectada
        };
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        handleInput(event) {
            const editableDiv = this.$refs.editableDiv;
            const urlPattern = /(https?:\/\/[^\s]+)/g;

            let content = editableDiv.innerText;
            const textNodes = Array.from(editableDiv.childNodes).filter(
                (node) => node.nodeType === Node.TEXT_NODE
            );

            textNodes.forEach((node) => {
                const urls = node.textContent.match(urlPattern);

                if (urls) {
                    urls.forEach((url) => {
                        const range = document.createRange();
                        range.setStart(node, node.textContent.indexOf(url));
                        range.setEnd(
                            node,
                            node.textContent.indexOf(url) + url.length
                        );

                        const badge = document.createElement("a");
                        badge.href = url;
                        badge.target = "_blank";
                        badge.style.backgroundColor = "#cfffe4";
                        badge.style.paddingTop = "3px";
                        badge.style.paddingBottom = "3px";
                        badge.style.paddingLeft = "6px";
                        badge.style.paddingRight = "6px";
                        badge.style.borderRadius = "4px";
                        badge.style.fontSize = "smaller";
                        badge.style.color = "#28c76f";
                        badge.style.fontWeight = "bold";
                        badge.textContent = url;

                        badge.onclick = () => window.open(url, "_blank");

                        // Elimina el contenido de la URL detectada y reemplázalo por el badge
                        range.deleteContents();
                        range.insertNode(badge);

                        // Inserta un espacio después del badge para evitar que se integre con el texto
                        const space = document.createTextNode("\u00A0");
                        badge.parentNode.insertBefore(space, badge.nextSibling);

                        this.moveCursorToEnd(editableDiv);
                    });
                }
            });
        },
        handlePaste(event) {
            event.preventDefault();

            const pastedData = (
                event.clipboardData || window.clipboardData
            ).getData("text");
            document.execCommand("insertText", false, pastedData);

            this.handleInput();
        },
        moveCursorToEnd(element) {
            const range = document.createRange();
            const selection = window.getSelection();
            range.selectNodeContents(element);
            range.collapse(false);
            selection.removeAllRanges();
            selection.addRange(range);
        },
        formattedDescripcion(descripcion) {
            // Expresión regular para detectar las URLs en el formato $b~d{}
            const urlPattern = /\$b~d\{(https?:\/\/[^\}]+)\}/g;

            return descripcion.replace(
                urlPattern,
                (match, url) =>
                    `<a href="${url}" target="_blank"
                style="
                    background-color: #cfffe4;
                    padding: 3px 6px;
                    border-radius: 4px;
                    font-size: smaller;
                    color: #28c76f;
                    font-weight: bold;
                    text-decoration: none;
                ">
                ${url}
            </a>`
            );
        },
        destroyComentario(id) {
            confirm(
                {
                    text: "¿Desea eliminar el comentario seleccionado?",
                },
                async () => {
                    try {
                        await this.$http({
                            method: "DELETE",
                            url: this.routeTo(
                                `${this.urlRoute}/virtual-classroom/recursos-foros-respuestas/destroy/${id}`
                            ),
                        });

                        alertSuccess("Comentario eliminado correctamente");
                        this.$inertia.reload({ only: ["comentarios"] });
                    } catch (error) {
                        console.error("Error");
                    }
                }
            );
        },
    },
};
</script>

<style scoped>
.foro {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.foro-container {
    display: flex;
    justify-content: start;
    flex-direction: column;
    gap: 10px;

    border: 1px solid #ebebeb;
    padding: 10px;
    background-color: #f9f9f9;

    .foro-container__title {
        color: rgb(80, 80, 80);
    }

    .foro-container__theme {
        color: rgb(80, 80, 80);
        font-size: 13px;
    }
}

.foro-respuestas {
    display: flex;
    justify-content: start;
    flex-direction: column;
    gap: 10px;

    border: 1px solid #ebebeb;
    border-top: none;
    padding: 10px;
}

.comentario {
    display: flex;
    align-items: center;
    gap: 10px;
    position: relative;

    .comentario-icon {
        .comentario-icon__style {
            padding: 8px 10px;
            border: 1px solid #afafaf;
            border-radius: 100%;
        }
    }

    .comentario-content {
        width: 100%;
        .comentario-content__name {
            color: #a0a0a0;
            font-size: 10px;
        }

        .comentario-content__respuesta {
            color: rgb(80, 80, 80);
        }
    }

    .comentario-options {
        display: flex;
        position: relative;

        .comentario-options__icons {
            display: flex;
            gap: 8px;

            position: absolute;
            bottom: 0;
            right: 0;
        }
    }
}

.foro-agregar-comentario {
    display: flex;
    justify-content: start;
    flex-direction: column;

    border: 1px solid #ebebeb;
    border-top: none;
    padding: 10px;
    background-color: #ffffff;

    .foro-agregar-comentario__container {
        border: 1px solid #ebebeb;

        .foro-agregar-comentario__box {
            border: none;
            margin: 0;
            padding: 10px;
            height: 100px;
            overflow: auto;

            input {
                border: none;
                height: 100%;
                width: 100%;
                padding: 10px;
                font-size: 12px;
            }
        }

        .foro-agregar-comentario__btn {
            display: flex;
            justify-content: end;
            background-color: white;
            border: none;
            margin: 0;
            padding: 10px;
        }
    }
}

.line {
    border-top: 1px solid #ebebeb;
    width: 100%;
    position: absolute;
    bottom: -5px;
}

.empty {
    p {
        font-size: 13px;
        padding-top: 4px;
    }
}

.placeholder {
    color: #a0a0a098;
}

.icon-options {
    position: relative;
}

.tooltip-options {
    display: none;
}

.icon-options:hover {
    .icon-d {
        color: red;
    }

    .icon-c {
        color: rgb(19, 38, 238);
    }

    .tooltip-options {
        display: block;
        position: absolute;
        top: -20px;
        /* right: 0; */

        border-radius: 6px;
        font-size: 8.5px;
        background-color: rgba(0, 0, 0, 0.79);
        padding: 3px 5px;
        color: white;
    }

    .tooltip-delete {
        right: 0;
    }
}
</style>
