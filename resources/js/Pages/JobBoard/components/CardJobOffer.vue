<template>
    <div class="job-card card shadow-sm border-0 mb-2" @click="handleClick">
        <div class="card-body-customize card-body">
            <!-- Contenido principal -->
            <div class="oferta flex-grow-1 me-lg-3">
                <div class="badges">
                    <template v-if="job.postulacion_rapida">
                        <Badge badge="full-color" name="Postulación rápida" />
                    </template>
                    <template v-if="job.multiples_vacantes">
                        <Badge badge="bordered" name="Multiples vacantes" />
                    </template>
                    <template v-if="job.nuevo">
                        <Badge badge="aqua" name="Nuevo" />
                    </template>
                    <span class="tiempo">{{ job.tiempo }}</span>
                </div>
                <div
                    class="oferta__empresa d-flex flex-column gap-1 justify-content-start align-items-start my-1"
                >
                    <h5 class="card-title p-0 m-0">
                        {{ job.puesto }}
                    </h5>
                    <span class="card-sub-title">{{ job.empresa }}</span>
                </div>
                <div>
                    <p class="card-text">Requisitos: {{ job.requisitos }}</p>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="job-info text-lg-end px-1">
                <div
                    class="job-info__ubicacion d-flex justify-content-start align-items-center"
                >
                    <feather-icon
                        icon="MapPinIcon"
                        size="15"
                        stroke-width="2.5"
                    />
                    <p class="text-muted m-0 p-0">{{ job.ubicacion }}</p>
                </div>
                <div
                    class="job-info__ubicacion d-flex justify-content-start align-items-center"
                >
                    <feather-icon
                        icon="HomeIcon"
                        size="15"
                        stroke-width="2.5"
                    />
                    <p class="text-muted m-0 p-0">{{ job.modalidad }}</p>
                </div>
                <div
                    class="job-info__discapacitados d-flex justify-content-start align-items-center"
                    v-if="job.apto_discapacidad"
                >
                    <span class="job-info__discapacitados-feather">
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
import Badge from "./Badge.vue";
export default {
    components: {
        Badge,
    },

    props: {
        job: {
            type: Object,
            required: true,
        },
    },
    methods: {
        handleClick() {
            this.$emit("click");
        },
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

    border-left-color: #7100c9;
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

.job-card {
    transition: all 0.2s ease-in-out;
}

.job-card:hover {
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

.job-info {
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

.job-info i {
    color: rgb(46, 46, 46) !important;
}

.job-info p {
    font-size: 0.9rem;
    color: rgb(46, 46, 46) !important;
}

.job-info__ubicacion {
    gap: 6px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.job-info__ubicacion p {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.job-info__discapacitados {
    gap: 6px;

    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;

    position: absolute;
    bottom: 0;
    right: 0;
}

.job-info__discapacitados p {
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

    .job-info {
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
    .job-info__discapacitados p {
        display: none;
    }
}

@media (max-width: 900px) {
    .job-info__discapacitados-feather {
        display: none;
    }
}
</style>
