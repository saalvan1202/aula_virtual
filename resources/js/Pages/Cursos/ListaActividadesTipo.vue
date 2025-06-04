<template>
    <LayoutContent>
        <div class="row">
            <div class="col-md-12 col-lg-12 mb-2">
                <button class="btn btn-secondary" @click="back()">
                    <feather-icon icon="ArrowLeftIcon" />
                    <span>Volver</span>
                </button>
            </div>
            <div class="col-md-12 col-lg-12 mb-1">
                <h4>{{ tipo_actividad.toUpperCase() }}</h4>
            </div>
            <div class="not-length" v-if="actividades.length == 0">
                <feather-icon icon="InboxIcon" />
                <span class="align-small">No tienes actividades</span>
            </div>
            <div class="col-md-12 col-lg-12" v-for="actividad in actividades">
                <ResourceCard
                    :resource="actividad"
                    :resource_type="tipo_actividad"
                    @click="
                        visit(
                            actividad.uuid,
                            actividad.id,
                            actividad.seccion,
                            actividad.estado_actividad
                        )
                    "
                />
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import ResourceCard from "../VirtualClassroom/components/ResourceCard.vue";
import { BBadge } from "bootstrap-vue";
import { convertDate } from "../../helpers";
import { alertError } from "../../sweetAlert2";

export default {
    name: "ListaActividadesTipo",
    components: {
        LayoutContent,
        Card,
        ResourceCard,
        BBadge,
    },
    props: {
        uuid: String,
        tipo_actividad: String,
        actividades: Array,
    },
    mounted() {
        console.log(this.actividades);
    },
    methods: {
        convertDate,
        visit(uuid, id, seccion, estado_actividad) {
            if (!estado_actividad) {
                alertError("La actividad no está disponible");
                return;
            }

            this.$inertia.visit(
                this.routeTo(
                    `gestion-cursos/virtual-classroom/${uuid}?id=${id}&seccion=${seccion}`
                )
            );
        },
        back() {
            this.$inertia.visit(
                this.routeTo(`gestion-cursos/estudiantes/${this.uuid}`)
            );
        },
    },
};
</script>
<style scoped>
.not-length {
    height: 50px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    margin: 20px;
}
.not-length .feather {
    height: 4rem !important;
}
.card {
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
    border-left: 4px solid #28c76f;
    padding-bottom: 20px; /* Espacio reservado para evitar salto */
    box-sizing: border-box; /* Incluye borde en el tamaño total */
    transform: translateY(0);
    margin-bottom: 20px;
}
.card:hover {
    transition: all 0.05s ease-in-out, border 0; /* Transición consistente */
    transform: translateY(-10px);
    box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.2);
    cursor: pointer;
}
.card-title {
    font-size: 13px;
    white-space: nowrap; /* Evita que el texto se divida en varias líneas */
    overflow: hidden; /* Oculta el texto que se desborda */
    text-overflow: ellipsis; /* Agrega los puntos suspensivos (...) al final */
    max-width: 1000px;
}
</style>
