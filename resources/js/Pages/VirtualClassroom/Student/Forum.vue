<template>
    <LayoutContent>
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
                        <p class="p-0 m-0">¡Se el primero en comentar!</p>
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
                        <div
                            v-if="index != comentarios.length - 1"
                            class="line"
                        ></div>
                    </div>
                </div>
                <div v-if="canComment" class="foro-agregar-comentario">
                    <div class="foro-agregar-comentario__container">
                        <div
                            class="foro-agregar-comentario__box"
                            contenteditable="true"
                            @input="handleInput"
                            @paste="handlePaste"
                            @focus="handleFocus"
                            @blur="handleBlur"
                            ref="editableDiv"
                        >
                            <p class="placeholder">Escribe un comentario...</p>
                        </div>
                        <div class="foro-agregar-comentario__btn">
                            <button
                                class="btn btn-secondary btn-sm rounded-0"
                                @click.prevent="handlePublish"
                            >
                                Publicar
                            </button>
                        </div>
                    </div>
                </div>
                <div v-else class="foro-agregar-comentario">
                    <div
                        class="empty d-flex flex-column justify-content-center align-items-center p-1"
                    >
                        <div>
                            <feather-icon icon="AwardIcon" size="30" />
                        </div>
                        <p class="p-0 m-0">
                            ¡Alcanzaste el maximo de comentarios posibles!
                        </p>
                    </div>
                </div>
            </template>
        </Card>
    </LayoutContent>
</template>

<script>
import { useForm } from "@inertiajs/vue2";
import Card from "../../../Components/Card.vue";
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import { capitalizeFirstWord } from "../../../utils/textProcess";
import { cleanObject } from "../../../utils/objectProcess";
import { alertError, alertSuccess } from "../../../sweetAlert2";

export default {
    components: {
        LayoutContent,
        Card,
    },
    name: "Forum",
    props: {
        urlRoute: String,
        foro: Object,
        comentarios: Array,
        id_usuario_estudiante: Number,
        id_curso_matricula: Number,
    },
    data() {
        return {
            formRespuesta: useForm({
                id_curso_matricula: this.id_curso_matricula,
                id_recurso_foro: this.foro.id,
                descripcion: "",
                url: "",
            }),
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
        handlePublish() {
            const editableDiv = this.$refs.editableDiv;
            const urlPattern = /(https?:\/\/[^\s]+)/g;

            const placeholder = editableDiv.querySelector(".placeholder");
            if (placeholder) {
                placeholder.remove();
            }

            // Obtener solo el texto ingresado en el div (sin etiquetas HTML)
            let content = editableDiv.innerText;

            // Buscar todas las URLs en el contenido
            const urls = content.match(urlPattern);

            if (urls) {
                this.formRespuesta.url = JSON.stringify(urls);

                // Crear un mapa de URLs formateadas para evitar problemas de doble reemplazo
                const urlMap = urls.reduce((map, url) => {
                    if (!map[url]) {
                        map[url] = `$b~d{${url}}`;
                    }
                    return map;
                }, {});

                // Reemplazar cada URL por su formato formateado
                content = content.replace(
                    urlPattern,
                    (matchedUrl) => urlMap[matchedUrl]
                );
            }

            this.formRespuesta.descripcion = content;

            const data = this.formRespuesta.data();

            this.$http({
                method: "POST",
                url: this.routeTo(
                    `${this.urlRoute}/virtual-classroom/recursos-foros-respuestas/store`
                ),
                data: data,
                headers: {
                    "X-Inertia-Error-Bag": "recursosForosTareas",
                },
            })
                .then(() => {
                    alertSuccess("Datos Guardados Correctamente");
                    this.$inertia.reload({ only: ["comentarios"] });

                    // Limpiar el formulario y el campo editable
                    this.formRespuesta.reset(); // Resetear los datos del formulario
                    editableDiv.innerHTML = ""; // Limpiar el contenido editable

                    // Opcional: Volver a mostrar el placeholder si es necesario
                    const placeholder =
                        editableDiv.querySelector(".placeholder");
                    if (placeholder) {
                        placeholder.style.display = "block";
                    }
                })
                .catch((err) => {
                    // console.log(err.response.data.error);
                    alertError(err.response.data.error);
                })
                .finally(() => {});
        },
        handleFocus() {
            const editableDiv = this.$refs.editableDiv;
            const placeholder = editableDiv.querySelector(".placeholder");
            if (placeholder) {
                placeholder.style.display = "none"; // Oculta el placeholder al hacer focus
            }
        },
        handleBlur() {
            const editableDiv = this.$refs.editableDiv;
            const placeholder = editableDiv.querySelector(".placeholder");

            // Si el contenido está vacío, muestra el placeholder
            if (!editableDiv.innerHTML.trim() && placeholder) {
                placeholder.style.display = "block"; // Muestra el placeholder si el contenido está vacío
            }
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
    },
    computed: {
        canComment() {
            const comentarios = this.comentarios;
            const id_usuario_estudiante = this.id_usuario_estudiante;

            const new_comentarios = comentarios.filter(
                (comentario) =>
                    comentario.estudiante.id_usuario_estudiante ===
                    id_usuario_estudiante
            );

            const userCanComment =
                new_comentarios[0]?.estudiante.puede_comentar;

            if (userCanComment == undefined) {
                return true;
            }

            // return true;
            return userCanComment;
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
    gap: 6px;
    p {
        font-size: 13px;
    }
}

.placeholder {
    color: #a0a0a098;
}
</style>
