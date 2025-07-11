<template>
    <LayoutContent>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Cuenta</h4>
            </div>
            <div class="card-content">
                <div class="card-body card-body-vue">
                    <BTabs
                        vertical
                        content-class="col-12 col-md-9 mt-1 mt-md-0"
                        pills
                        nav-wrapper-class="col-md-3 col-12"
                        nav-class="nav-left"
                    >
                        <BTab active>
                            <template #title>
                                <feather-icon
                                    icon="UserIcon"
                                    size="18"
                                    class="mr-50"
                                />
                                <span class="font-weight-bold">General</span>
                            </template>
                            <AccountGeneral :user="user" />
                        </BTab>
                        <BTab>
                            <template #title>
                                <feather-icon
                                    icon="LockIcon"
                                    size="18"
                                    class="mr-50"
                                />
                                <span class="font-weight-bold"
                                    >Cambiar Contraseña</span
                                >
                            </template>
                            <AccountPassword />
                        </BTab>
                        <BTab>
                            <template #title>
                                <feather-icon
                                    icon="SmartphoneIcon"
                                    size="18"
                                    class="mr-50"
                                />
                                <span class="font-weight-bold"
                                    >Dispositivos</span
                                >
                            </template>
                            <AccountDevices
                                :dispositivos="user.dispositivos"
                                :destroy="destroy"
                            />
                        </BTab>
                    </BTabs>
                </div>
            </div>
        </div>
    </LayoutContent>
</template>

<script>
import { BTabs, BTab } from "bootstrap-vue";
import LayoutContent from "../../Layouts/LayoutContent";
import AccountGeneral from "./AccountGeneral";
import AccountPassword from "./AccountPassword.vue";
import AccountDevices from "./AccountDevices.vue";
import { alertError, alertSuccess, confirm } from "../../sweetAlert2";
export default {
    name: "Account",
    props: {
        user: Object,
    },
    components: {
        AccountGeneral,
        LayoutContent,
        BTabs,
        BTab,
        AccountPassword,
        AccountDevices,
    },
    methods: {
        getDeviceIcon(dispositivo) {
            if (dispositivo.es_movil) return "SmartphoneIcon";
            if (dispositivo.es_tablet) return "TabletIcon";
            if (dispositivo.es_escritorio) return "MonitorIcon";
            return "HelpCircleIcon";
        },
        destroy(id) {
            confirm(
                {
                    text: "¿Desea eliminar este dispositivo?",
                },
                () => {
                    this.$http({
                        method: "DELETE",
                        url: this.routeTo(`dispositivos/${id}`),
                    })
                        .then((res) => {
                            alertSuccess("Eliminado Correctamente");
                            this.$inertia.reload({
                                only: ["user"],
                            });
                        })
                        .catch((err) => {
                            alertError(err.response.data.error);
                        });
                }
            );
        },
    },
};
</script>

<style scoped></style>
