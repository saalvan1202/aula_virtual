<template>
    <div class="question-content">
        <div>
            <h3>Pregunta {{ index + 1 }}</h3>
            <p>{{ capitalizeWord(descripcion) }}</p>
        </div>
        <div class="puntaje" v-if="!respuestas">
            <p class="m-0 p-0" :class="{ 'no-puntaje': puntaje == '0.00' }">
                {{
                    puntaje != "0.00"
                        ? `(${puntaje} punto(s))`
                        : "Sin puntaje asignado"
                }}
            </p>
            <slot></slot>
        </div>
        <div class="puntaje" v-else>
            <slot></slot>
            <p class="m-0 p-0">
                <span
                    ><strong>{{ scoreAsign(respuestas) }}</strong></span
                >{{ ` / ${puntaje} punto(s)` }}
            </p>
        </div>
    </div>
</template>

<script>
import { capitalizeFirstWord } from "../../../../utils/textProcess";

export default {
    name: "HeaderQuestion",
    props: {
        index: {
            type: Number,
        },
        descripcion: {
            type: String,
        },
        puntaje: {
            type: String,
        },
        respuestas: Object,
    },
    methods: {
        capitalizeWord(text) {
            return capitalizeFirstWord(text);
        },
        scoreAsign(respuestas) {
            if (respuestas.id_tipo_pregunta != 4) {
                return respuestas.puntaje_obtenido;
            } else {
                if (respuestas.respuestas[0].calificado == "N") {
                    return "No calificado";
                } else {
                    return respuestas.puntaje_obtenido;
                }
            }
        },
    },
    mounted() {
        console.log(this.respuestas);
    },
};
</script>

<style scoped>
.question-content {
    display: flex;
    justify-content: space-between;
}

.question-content h3 {
    margin: 0;
    font-size: 18px;
    color: #333;
}

.question-content p {
    margin: 8px 0;
    color: #666;
}

.puntaje {
    display: flex;
    justify-content: end;
    gap: 10px;
    align-items: center;
}

.puntaje p {
    text-align: center;
    font-size: 11px;
    color: #989898;
}

.no-puntaje {
    color: rgb(255, 67, 67) !important; /* Rojo para indicar falta de puntaje */
    font-weight: bold;
}
</style>
