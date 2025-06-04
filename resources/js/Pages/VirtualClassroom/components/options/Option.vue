<template>
    <div class="simple-selection-question">
        <HeaderQuestion
            :index="index"
            :descripcion="descripcion"
            :puntaje="puntaje"
            :respuestas="respuestas"
        >
            <div class="options-btn">
                <slot name="options-btn"></slot>
            </div>
        </HeaderQuestion>

        <div class="default-content">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import HeaderQuestion from "./HeaderQuestion.vue";

export default {
    name: "Option",
    components: {
        HeaderQuestion,
    },
    props: {
        descripcion: {
            type: String,
            required: true,
        },
        puntaje: {
            type: String,
        },
        index: {
            type: Number,
            required: false,
        },
        respuestas: Object,
    },
};
</script>

<style scoped>
.simple-selection-question {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 16px;
    background-color: #f9f9f9;
}
</style>
