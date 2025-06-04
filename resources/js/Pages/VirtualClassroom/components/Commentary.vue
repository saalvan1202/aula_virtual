<template>
    <div
        :class="
            secondary
                ? 'comentario secondary'
                : primary
                ? 'comentario primary'
                : 'comentario'
        "
        @click="handleClick"
    >
        <p class="m-0 p-0" v-html="formattedDescripcion(comentario)"></p>
        <div
            v-if="primary"
            class="ultimo-enviado"
            :class="{ 'danger-tag': danger_tag }"
        >
            <p class="m-0 p-0">{{ tag_text }}</p>
        </div>
    </div>
</template>
<script>
export default {
    name: "QualificationsForum",
    props: {
        comentario: {
            type: String,
            required: true,
        },
        primary: {
            type: Boolean,
            default: false,
        },
        secondary: {
            type: Boolean,
            default: false,
        },
        tag_text: {
            type: String,
            default: "Ultimo enviado",
        },
        danger_tag: {
            type: Boolean,
            default: false,
        },
    },
    methods: {
        formattedDescripcion(comentario) {
            // Expresión regular para detectar las URLs en el formato $b~d{}
            const urlPattern = /\$b~d\{(https?:\/\/[^\}]+)\}/g;

            return comentario.replace(
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
        handleClick() {
            this.$emit("click");
        },
    },
};
</script>
<style scoped>
.comentario {
    padding: 10px;
    border: 1px solid #bdbdbd;
    border-radius: 5px;
    font-size: 13px;

    display: flex;
    justify-content: space-between;
}

.comentario:hover {
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

.danger-tag {
    color: #dc3545; /* Color rojo para el texto */
}
</style>
