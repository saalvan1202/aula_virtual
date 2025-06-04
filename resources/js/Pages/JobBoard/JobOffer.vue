<template>
    <LayoutContent>
        <section>
            <div class="container-job row m-0 p-0">
                <div class="col-12 m-0 p-0 sticky-element">
                    <div class="card m-0 p-0">
                        <div class="card-content">
                            <div class="header card-body card-body-vue">
                                <h4 class="p-0 m-0">
                                    {{ oferta.puesto }}
                                </h4>
                                <p class="p-0 m-0">{{ oferta.empresa }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <section>
                    <div class="col-12 m-0 p-0 sticky-element">
                        <div class="card m-0 p-0">
                            <div class="card-content">
                                <div
                                    class="sub-header card-body card-body-vue m-0 p-0"
                                >
                                    <div
                                        :class="[
                                            'sub-header-titles',
                                            sectionSelected === 'DE'
                                                ? 'sub-header-titles-select'
                                                : '',
                                        ]"
                                        @click="updateSelect('DE')"
                                    >
                                        <p class="m-0 p-0">
                                            Detalle del empleo
                                        </p>
                                    </div>
                                    <div
                                        :class="[
                                            'sub-header-titles',
                                            sectionSelected === 'ER'
                                                ? 'sub-header-titles-select'
                                                : '',
                                        ]"
                                        @click="updateSelect('ER')"
                                    >
                                        <p class="m-0 p-0">
                                            Empleos relacionados
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 m-0 p-0" ref="habilidadesSection">
                        <div class="card m-0 p-0">
                            <div class="card-content">
                                <div class="card-body card-body-vue info-jobs">
                                    <div class="badges">
                                        <template
                                            v-if="oferta.postulacion_rapida"
                                        >
                                            <Badge
                                                badge="full-color"
                                                name="Postulación rápida"
                                            />
                                        </template>
                                        <template
                                            v-if="oferta.multiples_vacantes"
                                        >
                                            <Badge
                                                badge="bordered"
                                                name="Multiples vacantes"
                                            />
                                        </template>
                                        <template v-if="oferta.nuevo">
                                            <Badge badge="aqua" name="Nuevo" />
                                        </template>
                                        <span class="tiempo">{{
                                            oferta.tiempo
                                        }}</span>
                                    </div>
                                    <div
                                        class="info-jobs__ubication d-flex justify-content-start align-items-center"
                                    >
                                        <feather-icon
                                            icon="MapPinIcon"
                                            size="17"
                                            stroke-width="2.5"
                                        />
                                        <span>{{ oferta.ubicacion }}</span>
                                    </div>
                                    <div class="descripcion-puesto">
                                        <div
                                            class="descripcion-puesto__title d-flex justify-content-start align-items-center"
                                        >
                                            <feather-icon
                                                icon="FileTextIcon"
                                                size="20"
                                                stroke-width="2.5"
                                            />
                                            <p class="text-black m-0 p-0">
                                                Descripción del puesto
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-black m-0 p-0 text-justify">
                                        {{ oferta.descripcion_puesto }}
                                    </p>
                                    <div class="sub-infos">
                                        <p class="text-black title m-0 p-0">
                                            Requisitos:
                                        </p>
                                        <p class="m-0 p-0 text-justify">
                                            {{ oferta.requisitos }}
                                        </p>
                                    </div>
                                    <div class="sub-infos">
                                        <p class="text-black title m-0 p-0">
                                            Contacto:
                                        </p>
                                        <div class="etiqueta">
                                            <feather-icon
                                                icon="BoxIcon"
                                                size="15"
                                                stroke-width="2.5"
                                            />
                                            <p
                                                class="m-0 p-0 text-info-empresa"
                                            >
                                                Empresa: {{ oferta.ruc }} -
                                                {{ oferta.empresa }}
                                            </p>
                                        </div>
                                        <div class="etiqueta">
                                            <feather-icon
                                                icon="PhoneIcon"
                                                size="15"
                                                stroke-width="2.5"
                                            />
                                            <p
                                                class="m-0 p-0 text-info-empresa"
                                            >
                                                Telefono: {{ oferta.telefono }}
                                            </p>
                                        </div>
                                        <div class="etiqueta">
                                            <feather-icon
                                                icon="AtSignIcon"
                                                size="15"
                                                stroke-width="2.5"
                                            />
                                            <p
                                                class="m-0 p-0 text-info-empresa"
                                            >
                                                Correo: {{ oferta.correo }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="info-tags">
                                        <div class="container-tags">
                                            <div class="etiqueta">
                                                <feather-icon
                                                    icon="HomeIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    {{ oferta.modalidad }}
                                                </p>
                                            </div>
                                            <div class="etiqueta">
                                                <feather-icon
                                                    icon="CreditCardIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    {{ oferta.salario }}
                                                </p>
                                            </div>
                                            <div class="etiqueta">
                                                <feather-icon
                                                    icon="ClockIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    {{ oferta.horario }}
                                                </p>
                                            </div>
                                            <div class="etiqueta">
                                                <feather-icon
                                                    icon="AwardIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    {{ oferta.nivel }}
                                                </p>
                                            </div>
                                            <div class="etiqueta">
                                                <feather-icon
                                                    icon="UsersIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    {{ oferta.numero_vacantes }}
                                                </p>
                                            </div>
                                            <div
                                                class="etiqueta"
                                                v-if="
                                                    oferta.disponibilidad_viajar
                                                "
                                            >
                                                <feather-icon
                                                    icon="SendIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    Disponibilidad para viajar
                                                </p>
                                            </div>
                                            <div
                                                v-if="oferta.apto_discapacidad"
                                                class="etiqueta"
                                            >
                                                <feather-icon
                                                    icon="HeartIcon"
                                                    size="15"
                                                    stroke-width="2.5"
                                                />
                                                <p class="m-0 p-0">
                                                    Apto discapacidad
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 m-0 p-0">
                        <div class="card m-0 p-0">
                            <div class="card-content">
                                <div
                                    class="card-body card-body-vue habilidades"
                                >
                                    <div class="sub-infos">
                                        <p class="text-black title m-0 p-0">
                                            Funciones a realizar
                                        </p>
                                        <p class="m-0 p-0 text-justify">
                                            {{ oferta.funciones }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="relacionados col-12 m-0 p-0"
                        ref="relacionadosSection"
                    >
                        <div class="pt-1 title">
                            <p>Empleos relacionados</p>
                        </div>
                        <div class="card m-0 p-0 mt-1 mb-3">
                            <div class="card-content">
                                <div class="card-body card-body-vue">
                                    <template
                                        v-if="ofertas_relacionadas.length != 0"
                                    >
                                        <div
                                            v-for="oferta_relacionada in ofertas_relacionadas"
                                            :key="oferta_relacionada.uuid"
                                        >
                                            <CardJobOffer
                                                :job="oferta_relacionada"
                                                @click="
                                                    redirect(
                                                        oferta_relacionada.uuid
                                                    )
                                                "
                                            />
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div
                                            class="d-flex flex-column justify-content-center align-items-center mt-1"
                                        >
                                            <div>
                                                <feather-icon
                                                    icon="InboxIcon"
                                                    class="icon-large"
                                                />
                                            </div>
                                            <p>No hay ofertas relacionadas</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </LayoutContent>
</template>
<script>
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
import Button from "../../Components/Button.vue";
import Tag from "./components/Tag.vue";
import Badge from "./components/Badge.vue";
import CardJobOffer from "./components/CardJobOffer.vue";
export default {
    components: {
        LayoutContent,
        Card,
        Button,
        Tag,
        Badge,
        CardJobOffer,
    },
    name: "StudyPlan",
    props: {
        urlRoute: String,
        oferta: Object,
        ofertas_relacionadas: Array,
    },
    data() {
        return {
            sectionSelected: "DE",
            observer: null,
        };
    },
    mounted() {
        this.initObserver();
    },
    methods: {
        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`oferta_laboral/${name}`));
        },

        updateSelect(section) {
            this.sectionSelected = section;

            // Si la sección seleccionada es "ER", desplázate a la sección "Empleos relacionados"
            if (section === "ER") {
                this.$nextTick(() => {
                    this.$refs.relacionadosSection.scrollIntoView({
                        behavior: "smooth", // Desplazamiento suave
                        block: "start", // Alinea la sección al inicio
                    });
                });
            }
        },

        // Inicializa Intersection Observer
        initObserver() {
            const options = {
                root: null, // El viewport
                threshold: 0.5, // El 50% del div debe ser visible
            };

            this.observer = new IntersectionObserver(
                this.handleIntersection,
                options
            );

            // Observa el div de "Empleos relacionados"
            const relacionadosTarget = this.$refs.relacionadosSection;
            if (relacionadosTarget) {
                this.observer.observe(relacionadosTarget);
            }

            // Observa el div de "Conocimientos y habilidades valorados"
            const habilidadesTarget = this.$refs.habilidadesSection;
            if (habilidadesTarget) {
                this.observer.observe(habilidadesTarget);
            }
        },

        handleIntersection(entries) {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    if (entry.target === this.$refs.relacionadosSection) {
                        this.sectionSelected = "ER";
                    } else if (entry.target === this.$refs.habilidadesSection) {
                        this.sectionSelected = "DE";
                    }
                }
            });
        },
    },
    beforeDestroy() {
        // Limpia el observer cuando se destruya el componente
        if (this.observer) {
            this.observer.disconnect();
        }
    },
};
</script>

<style scoped>
.container-job,
.container-job section {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.container-job section {
    gap: 15px;
}

.header {
    display: flex;
    flex-direction: column;
    justify-content: start;
    gap: 4px;
}

.header h4 {
    font-weight: 600;
    color: black;
}

.header p {
    font-weight: 600;
    font-size: 13px;
    text-decoration: underline;
}

.sub-header {
    display: flex;
}

.sub-header-titles {
    display: flex;
    justify-content: center;
    align-items: center;

    width: 50%;
    padding: 14px;

    cursor: pointer;
}

.sub-header-titles p {
    font-weight: 600;
    font-size: 14.5px;
}

.sub-header-titles-select {
    padding-top: 18px;
    border-bottom: 4px solid #7100c9;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}

.sub-header-titles-select p {
    color: #7100c9;
}

.badges {
    display: flex;
    gap: 10px;
    justify-content: start;
    align-items: center;
}

.tiempo {
    font-size: 0.8rem;
    font-weight: 600;
    color: #676676 !important;
}

.info-jobs {
    display: flex;
    flex-direction: column;
    gap: 16px;
    color: black;
}

.info-jobs__ubication {
    gap: 12px;
    color: black;

    span {
        font-weight: lighter;
        color: #6e6b7b;
    }
}

.descripcion-puesto__title {
    display: flex;
    justify-content: start;
    align-items: center;
    gap: 14px;

    p {
        font-size: 18px;
        font-weight: 600;
    }
}

.sub-infos {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.sub-infos .title {
    font-size: 16px;
    font-weight: 600;
}

.info-tags {
    border-top: 1px solid #d3d3d3;
    padding-top: 13px;
}

.container-tags {
    display: grid;
    grid-template-columns: repeat(3, 1fr);

    gap: 12px;
}

.etiqueta {
    display: flex;
    justify-content: start;
    align-items: center;
    gap: 8px;
}

.etiqueta p {
    color: #6e6b7b;
}

.habilidades {
    color: black;
}

.habilidades .title {
    font-size: 14px;
    font-weight: 600;
    color: #7100c9;
}

.relacionados {
    border-top: 1px solid #d3d3d3;

    .title {
        color: black;
        font-weight: 600;
        font-size: 16px;
    }
}

.text-info-empresa {
    color: black !important;
}

.text-justify {
    text-align: justify;
}
</style>
