<template>
    <b-nav-item-dropdown
        class="dropdown-notification mr-25"
        menu-class="dropdown-menu-media"
        right
    >
        <template #button-content>
            <feather-icon
                style="color: white !important"
                :badge="`${
                    notifications.length > 0 ? notifications.length : ''
                }`"
                badge-classes="bg-danger"
                class="text-body"
                icon="BellIcon"
                size="21"
            />
        </template>

        <!-- Header -->
        <li class="dropdown-menu-header">
            <div class="dropdown-header d-flex">
                <h4 class="notification-title mb-0 mr-auto">Notificaciones</h4>
                <b-badge pill variant="light-success">
                    {{ notifications.length }} Notificaciones
                </b-badge>
            </div>
        </li>
        <div class="not-length" v-if="notifications.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small">No tienes notificaciones</span>
        </div>
        <!-- Notifications -->
        <vue-perfect-scrollbar
            v-else
            class="scrollable-container media-list scroll-area"
            tagname="li"
        >
            <!-- Account Notification -->
            <b-link
                title="Ir a actividad"
                v-for="notification in notifications"
                :key="notification.id"
                @click="
                    visit(
                        notification.uuid,
                        notification.id,
                        notification.seccion
                    )
                "
            >
                <b-media>
                    <template #aside>
                        <section style="display: flex; flex-direction: column">
                            <b-avatar
                                :variant="`${
                                    notification.tipo_actividad == 'Tareas'
                                        ? 'success'
                                        : notification.tipo_actividad == 'Foros'
                                        ? 'primary'
                                        : 'info'
                                }`"
                            >
                                <feather-icon :icon="notification.icono" />
                            </b-avatar>
                            <span style="color: darkgray">{{
                                notification.tipo_actividad
                            }}</span>
                        </section>
                    </template>
                    <p
                        class="media-heading"
                        style="display: flex; flex-direction: column"
                    >
                        <span
                            class="font-weight-bolder"
                            :title="notification.descripcion"
                        >
                            {{ notification.descripcion }}
                        </span>
                        <span class="font-weight-normal">
                            {{ notification.curso }}
                        </span>
                        <span
                            class="font-weight-normal"
                            style="font-size: 11px"
                        >
                            {{
                                convertDate(notification.fecha_inicio, "-", "/")
                            }}
                            -
                            {{ convertDate(notification.fecha_fin, "-", "/") }}
                        </span>
                        <span
                            class="font-weight-normal"
                            style="font-size: 11px"
                        >
                            {{ notification.hora_inicio }} -
                            {{ notification.hora_fin }}
                        </span>
                    </p>
                    <small class="notification-text">
                        <BBadge variant="light-warning">
                            {{ notification.estado_notificacion }}
                        </BBadge>
                    </small>
                </b-media>
            </b-link>
        </vue-perfect-scrollbar>
    </b-nav-item-dropdown>
</template>

<script>
import {
    BNavItemDropdown,
    BBadge,
    BMedia,
    BLink,
    BAvatar,
    BButton,
    BFormCheckbox,
} from "bootstrap-vue";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
import { convertDate } from "../../helpers";
export default {
    components: {
        BNavItemDropdown,
        BBadge,
        BMedia,
        BBadge,
        BLink,
        BAvatar,
        VuePerfectScrollbar,
        BButton,
        BFormCheckbox,
    },
    data() {
        return {
            notifications: [],
        };
    },
    mounted() {
        this.notificaciones();
    },
    methods: {
        convertDate,
        visit(uuid, id, seccion) {
            console.log(id, seccion);
            this.$inertia.visit(
                this.routeTo(
                    `gestion-cursos/virtual-classroom/${uuid}?id=${id}&seccion=${seccion}`
                )
            );
        },
        notificaciones() {
            this.$http
                .get(this.routeTo(`notificaciones`))
                .then((res) => {
                    this.notifications = res.data;
                })
                .catch((error) => {})
                .finally(() => {});
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
.truncated-text {
    white-space: nowrap; /* Evita que el texto se divida en varias líneas */
    overflow: hidden; /* Oculta el texto que se desborda */
    text-overflow: ellipsis; /* Agrega los puntos suspensivos (...) al final */
    max-width: 180px;
}
</style>
