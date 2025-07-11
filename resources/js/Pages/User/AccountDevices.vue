<template>
    <div class="row">
        <div class="col-12">
            <div
                v-if="dispositivos.length === 0"
                class="text-muted w-full d-flex justify-content-center align-items-center h-100"
            >
                No hay dispositivos vinculados
            </div>

            <div v-else class="row">
                <div
                    v-for="(d, index) in dispositivos"
                    :key="index"
                    class="col-md-4 mb-3"
                >
                    <div class="card shadow-sm h-100 border position-relative">
                        <!-- Botón de desvincular -->
                        <feather-icon
                            class="position-absolute cursor-pointer text-danger"
                            icon="XIcon"
                            size="16"
                            style="top: 10px; right: 10px; z-index: 10"
                            @click="destroy(d.id)"
                        />

                        <div class="card-body d-flex align-items-center">
                            <feather-icon
                                :icon="getDeviceIcon(d)"
                                class="text-primary mr-2"
                                size="60"
                            />
                            <div>
                                <div class="font-weight-bold">
                                    {{ d.so }} {{ d.version_so }} |
                                    <small class="text-muted">
                                        {{ d.dispositivo || "Desconocido" }}
                                    </small>
                                </div>
                                <small class="text-muted">IP: {{ d.ip }}</small
                                ><br />
                                <small class="text-muted">
                                    {{
                                        d.es_movil
                                            ? "Móvil"
                                            : d.es_escritorio
                                            ? "Escritorio"
                                            : d.es_tablet
                                            ? "Tablet"
                                            : ""
                                    }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { BInputGroupAppend } from "bootstrap-vue";
import InputError from "../../Components/InputError.vue";
export default {
    name: "AccountDevices",
    components: {
        BInputGroupAppend,
        InputError,
    },
    props: {
        dispositivos: Array,
        destroy: Function,
    },
    data() {
        return {
            form: this.$inertia.form({
                current_password: "",
                new_password: "",
                new_password_confirmation: "",
            }),
            passwordFieldTypeCurrent: "password",
            passwordFieldTypeNew: "password",
            passwordFieldTypeRetype: "password",
        };
    },
    methods: {
        getDeviceIcon(dispositivo) {
            if (dispositivo.es_movil) return "SmartphoneIcon";
            if (dispositivo.es_tablet) return "TabletIcon";
            if (dispositivo.es_escritorio) return "MonitorIcon";
            return "HelpCircleIcon";
        },
    },
    computed: {
        errors() {
            return this.$page.props.errors.password || {};
        },
    },
};
</script>

<style scoped></style>
