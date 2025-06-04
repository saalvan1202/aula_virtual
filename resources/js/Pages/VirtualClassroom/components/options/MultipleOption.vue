<template>
    <div class="options" v-if="!respuestas">
        <div
            v-for="(option, index) in opciones"
            :key="option.id"
            class="options-item"
            @click="toggleOption(option.id)"
            :class="{ disabled: disabled }"
        >
            <div class="question-icon">
                <feather-icon
                    :icon="
                        selectedOptions.includes(option.id) ||
                        option.correcta === 'S'
                            ? 'CheckSquareIcon'
                            : 'SquareIcon'
                    "
                    :style="{
                        color: selectedOptions.includes(option.id)
                            ? '#007bff'
                            : option.correcta === 'S'
                            ? '#28a745'
                            : '',
                    }"
                    size="18"
                />
            </div>
            <div
                class="option"
                style="width: 100%"
                :class="{
                    selected: selectedOptions.includes(option.id),
                    correct: option.correcta === 'S',
                    'correct-disabled': disabled && option.correcta === 'S',
                }"
            >
                <span class="option-index">{{ indexAlfabetico(index) }}.</span>
                {{ option.descripcion }}
            </div>
        </div>
    </div>
    <div class="options" v-else>
        <div
            v-for="(option, index) in opciones"
            :key="option.id"
            class="options-item"
            :class="{ disabled: !esRespuestaMarcada(option.id) }"
        >
            <div class="question-icon">
                <feather-icon
                    :icon="
                        esRespuestaCorrecta(option.id) &&
                        esRespuestaMarcada(option.id)
                            ? 'CheckSquareIcon'
                            : esRespuestaMarcada(option.id)
                            ? 'XSquareIcon'
                            : 'SquareIcon'
                    "
                    :style="{
                        color:
                            esRespuestaCorrecta(option.id) &&
                            esRespuestaMarcada(option.id)
                                ? '#28a745'
                                : esRespuestaMarcada(option.id)
                                ? '#dc3545'
                                : '',
                    }"
                    size="18"
                />
            </div>
            <div
                class="option"
                style="width: 100%"
                :class="{
                    correct_2: esRespuestaCorrecta(option.id),
                    incorrect:
                        esRespuestaMarcada(option.id) &&
                        !esRespuestaCorrecta(option.id),
                    'disabled-option': !esRespuestaMarcada(option.id),
                }"
            >
                <span class="option-index">{{ indexAlfabetico(index) }}.</span>
                {{ capitalizeWord(option.descripcion) }}
            </div>
        </div>
    </div>
</template>

<script>
import { capitalizeFirstWord } from "../../../../utils/textProcess";
import HeaderQuestion from "./HeaderQuestion.vue";

export default {
    name: "MultipleOption",
    components: {
        HeaderQuestion,
    },
    props: {
        idPregunta: Number,
        opciones: {
            type: Array,
            required: true,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        respuestas: Array,
    },
    data() {
        return {
            selectedOptions: [],
        };
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        toggleOption(id) {
            if (this.disabled) return;
            if (this.selectedOptions.includes(id)) {
                this.selectedOptions = this.selectedOptions.filter(
                    (opt) => opt !== id
                );
            } else {
                this.selectedOptions.push(id);
            }
            this.emitSelectedOption();
        },
        indexAlfabetico(index) {
            return String.fromCharCode(65 + index).toLowerCase();
        },
        emitSelectedOption() {
            this.$emit(
                "update-selection",
                this.idPregunta,
                this.selectedOptions
            );
        },
        esRespuestaCorrecta(id) {
            return this.opciones.some(
                (option) => option.id === id && option.correcta === "S"
            );
        },
        esRespuestaMarcada(id) {
            return this.respuestas?.some(
                (respuesta) => respuesta.id_recurso_pregunta_alternativa === id
            );
        },
    },
};
</script>

<style scoped>
.question-icon {
    font-size: 24px;
    display: flex;
    align-items: center;
}
.options {
    margin-top: 16px;
}
.options-item {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 8px;
    cursor: pointer;
}
.option {
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    background-color: white;
}
.option:hover {
    background-color: #f0f0f0;
}
.option.selected {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}
.option.correct {
    background-color: #28a745 !important;
    color: white;
    border-color: #28a745 !important;
}
.option.correct_2 {
    background-color: #28a745;
    color: white;
    border-color: #28a745;
}
.option.incorrect {
    background-color: #dc3545;
    color: white;
    border-color: #dc3545;
}
.option.disabled-option {
    background-color: #f5f5f5;
    border-color: #ccc;
    color: #999;
    cursor: not-allowed;
}
.option-index {
    font-weight: bold;
    margin-right: 8px;
}
.options-item.disabled {
    cursor: not-allowed;
    opacity: 0.6;
}
.options-item.disabled .option {
    background-color: #f5f5f5;
    border-color: #ccc;
    cursor: not-allowed;
}
</style>
