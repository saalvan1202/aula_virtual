<template>
    <div
        class="resource-card card shadow-sm border-0 mb-2"
        @click="handleClick"
    >
        <div class="card-body-customize card-body">
            <!-- Contenido principal -->
            <div class="oferta flex-grow-1 me-lg-3">
                <div class="badges">
                    <template>
                        <Badge
                            badge="full-color"
                            :name="
                                capitalizeFirstWordAndConvertToSingular(
                                    resource_type
                                )
                            "
                            :custom_icon="resource.icono"
                            :green_mode="true"
                        />
                    </template>
                    <template>
                        <Badge
                            badge="conditional"
                            :conditional_owner="resource.estado_actividad"
                        />
                    </template>
                    <template v-if="resource.nuevo">
                        <Badge badge="aqua" name="Nuevo" />
                    </template>
                </div>
                <div
                    class="oferta__empresa d-flex flex-column gap-1 justify-content-start align-items-start my-1"
                >
                    <h5 class="card-title p-0 m-0">
                        {{ capitalizeFirstWord(resource.nombre) }}
                    </h5>
                    <span class="card-sub-title">{{
                        resource.nombre_curso
                    }}</span>
                </div>
                <div>
                    <p class="card-text">
                        Descripcion:
                        {{
                            resource.descripcion
                                ? capitalizeFirstWord(resource.descripcion)
                                : "Sin descripción"
                        }}
                    </p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="resource-info text-lg-end px-1">
                <div
                    class="resource-info__ubicacion d-flex justify-content-start align-items-center"
                >
                    <feather-icon
                        icon="ClockIcon"
                        size="15"
                        stroke-width="2.5"
                    />
                    <p class="text-muted m-0 p-0">
                        <strong>F. Inicio:</strong>
                        {{ convertDate(resource.fecha_inicio, "-", "/") }}
                        {{ resource.hora_inicio }}
                    </p>
                </div>
                <div
                    class="resource-info__ubicacion d-flex justify-content-start align-items-center"
                >
                    <feather-icon
                        icon="CalendarIcon"
                        size="15"
                        stroke-width="2.5"
                    />
                    <p class="text-muted m-0 p-0">
                        <strong>F. Fin:</strong>
                        {{ convertDate(resource.fecha_fin, "-", "/") }}
                        {{ resource.hora_fin }}
                    </p>
                </div>
                <div
                    class="resource-info__discapacitados d-flex justify-content-start align-items-center"
                    v-if="resource.apto_discapacidad"
                >
                    <span class="resource-info__discapacitados-feather">
                        <feather-icon
                            icon="HeartIcon"
                            size="12"
                            stroke-width="2.5"
                        />
                    </span>
                    <p class="text-muted m-0 p-0">Apto discapacidad</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Badge from "../../JobBoard/components/Badge.vue";
import {
    capitalizeFirstWord,
    capitalizeFirstWordAndConvertToSingular,
} from "../../../utils/textProcess";
import { convertDate } from "../../../helpers";

export default {
    name: "ResourceCard",
    components: {
        Badge,
    },

    props: {
        resource: {
            type: Object,
            required: true,
        },
        resource_type: {
            type: String,
            required: true,
        },
    },
    methods: {
        handleClick() {
            this.$emit("click");
        },
        capitalizeFirstWord,
        capitalizeFirstWordAndConvertToSingular,
        convertDate,
    },
};
</script>

<style scoped>
.card-body-customize {
    border-radius: 8px;
    cursor: pointer;
    border-style: solid;
    border-width: 1px;
    border-color: #e7e7e7;

    border-left-color: #39c239;
    border-left-width: 8px;

    display: flex;
    gap: 16px;

    min-height: 180px;
}

.oferta {
    width: 65%;
}

.badges {
    display: flex;
    gap: 10px;
    justify-content: start;
    align-items: center;
}

.resource-card {
    transition: all 0.2s ease-in-out;
    padding-bottom: 0 !important;
}

.resource-card:hover {
    transform: translateY(-4px);
    box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
}

.card-title {
    font-weight: 700;
    font-size: 1.1rem;
    color: rgb(69, 69, 69);
}

.card-sub-title {
    font-weight: 700;
    font-size: 0.9rem;
    color: rgb(69, 69, 69);
    padding-top: 8px;
}

.card-text {
    color: #3f3f3f;
    font-size: small;
    text-align: justify;

    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box; /* Necesario para el soporte de múltiples líneas */
    -webkit-line-clamp: 2; /* Número de líneas visibles */
    -webkit-box-orient: vertical; /* Configura la orientación para el clamping */
}

.tiempo {
    font-size: 0.8rem;
    font-weight: 600;
    color: #676676 !important;
}

.resource-info {
    display: flex;
    flex-direction: column;
    width: 35%;
    color: rgb(46, 46, 46) !important;
    position: relative;

    border-left-style: solid;
    border-left-width: 1px;
    border-left-color: #e7e7e7;
    gap: 5px;
}

.resource-info i {
    color: rgb(46, 46, 46) !important;
}

.resource-info p {
    font-size: 0.9rem;
    color: rgb(46, 46, 46) !important;
}

.resource-info__ubicacion {
    gap: 6px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.resource-info__ubicacion p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.resource-info__discapacitados {
    gap: 6px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    position: absolute;
    bottom: 0;
    right: 0;
}

.resource-info__discapacitados p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    font-size: 0.8rem;
    font-weight: 600;
    color: #676676 !important;
}

@media (max-width: 1370px) {
    .card-body-customize {
        flex-direction: column;
    }

    .oferta {
        width: 100%;
    }

    .resource-info {
        flex-direction: row;
        width: 100%;

        border-left-style: none;
        gap: 15px;

        padding-right: 0 !important;
        padding-left: 0 !important;
    }
}

@media (max-width: 800px) {
    .oferta__empresa {
        margin-top: 0 !important;
    }

    .badges {
        display: none;
    }
}

@media (max-width: 1250px) {
    .resource-info__discapacitados p {
        display: none;
    }
}

@media (max-width: 900px) {
    .resource-info__discapacitados-feather {
        display: none;
    }
}
</style>
