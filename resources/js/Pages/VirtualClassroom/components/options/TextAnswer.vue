<template>
    <div :class="['answer-input', { disabled: disabled }]">
        <feather-icon icon="FileTextIcon" size="18" class="input-icon" />
        <textarea
            v-model="answer"
            placeholder="Escribe tu respuesta aquí..."
            @input="updateAnswer"
            :style="{ height: textareaHeight }"
            rows="1"
            :disabled="disabled"
        ></textarea>
    </div>
</template>

<script>
export default {
    name: "TextAnswer",
    props: {
        idPregunta: Number, // ID de la pregunta para actualizar la respuesta correcta
        disabled: {
            type: Boolean,
            default: false,
        },
        respuestas: Array,
    },
    data() {
        return {
            answer: "",
            textareaHeight: "auto",
        };
    },
    methods: {
        updateAnswer() {
            if (this.disabled) return;
            this.$emit("update-text-answer", {
                idPregunta: this.idPregunta,
                textAnswer: this.answer, // Enviar la respuesta escrita
            });
            this.adjustHeight();
        },
        adjustHeight() {
            this.$nextTick(() => {
                const textarea = this.$el.querySelector("textarea");
                this.textareaHeight = `${textarea.scrollHeight}px`;
            });
        },
        loadAnswer() {
            if (this.respuestas) {
                const respuesta_text = this.respuestas[0].respuesta;
                if (respuesta_text) {
                    this.answer = respuesta_text;
                } else {
                    this.answer = "[ Sin respuesta ]";
                }
            }
        },
    },
    mounted() {
        this.loadAnswer();
    },
};
</script>

<style scoped>
.question-content h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.question-content p {
    margin: 8px 0;
    color: #666;
}

.answer-input {
    display: flex;
    align-items: flex-start;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 8px;
    background-color: white;
    transition: background-color 0.3s ease, border-color 0.3s ease;

    textarea {
        font-size: 13px;
    }
}

.input-icon {
    margin-right: 8px;
    margin-top: 2px;
    color: #888;
}

textarea {
    border: none;
    outline: none;
    width: 100%;
    font-size: 16px;
    resize: none; /* Evitar que el usuario redimensione el textarea manualmente */
    overflow: hidden; /* Elimina la barra de desplazamiento */
}

/* Estilo cuando el contenedor answer-input está deshabilitado */
.answer-input.disabled {
    background-color: #f5f5f5;
    border-color: #ccc;
    cursor: not-allowed;
}

textarea:disabled {
    background-color: #f5f5f5;
    border-color: #ccc;
    cursor: not-allowed;
}
</style>
