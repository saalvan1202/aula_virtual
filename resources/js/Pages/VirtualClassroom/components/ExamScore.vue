<template>
    <div class="nota">
        <span
            ><strong
                >{{
                    estado_examen == "PENDIENTE" ? "N. Temporal" : "Nota"
                }}:</strong
            ></span
        >
        <BBadge
            :variant="
                parseFloat(puntaje_examen) >= 10.5
                    ? 'light-success'
                    : 'light-danger'
            "
        >
            <p class="p-0 m-0">
                {{ puntaje_examen }}
            </p>
        </BBadge>
    </div>
</template>
<script>
import { BBadge } from "bootstrap-vue";

export default {
    name: "ExamScore",
    components: {
        BBadge,
    },
    props: {
        puntaje_examen: {
            type: String,
            required: true,
        },
        estado_examen: {
            type: String,
            default: false,
        },
    },
};
</script>
<style scoped>
.nota {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 4px;
    border: 1px solid #ddd;
    background-color: #f9f9f9;
    padding: 2px 12px;
    border-radius: 8px;

    p {
        font-size: 14px;
    }
}
</style>
