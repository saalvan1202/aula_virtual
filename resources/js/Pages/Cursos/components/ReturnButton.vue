<template>
    <div class="row">
        <div class="col-md-12 col-lg-12 mb-1">
            <Button
                ref="btnEdit"
                class="btn btn-secondary"
                @click="remember(path_remove)"
            >
                <feather-icon icon="ArrowLeftIcon" class="mr-50" />
                <span class="align-small">Volver</span>
            </Button>
        </div>
    </div>
</template>
<script>
export default {
    name: "ReturnButton",
    props: {
        title: {
            type: String,
            default: "Volver",
        },
        path_remove: {
            type: String,
            required: true,
        },
    },
    methods: {
        remember(path_remove) {
            const path = window.location.pathname;
            const parts = path.split("/");

            // Eliminar la primera parte ("pvirtual_instituto")
            parts.splice(0, 2); // Elimina los primeros 2 elementos (el primer elemento es un string vacío)

            // Unir las partes restantes con "/"
            const desired_path = parts.join("/");

            console.log("route", desired_path);
            console.log("path_remove", path_remove);

            // Escapar caracteres especiales en path_remove
            const escapedPath = path_remove.replace(
                /[.*+?^${}()|[\]\\]/g,
                "\\$&"
            );

            // Usamos una expresión regular para eliminar la coincidencia exacta, ya sea en el medio o al final
            const regex = new RegExp(`/${escapedPath}(?=/|$)`, "g");
            let new_url = desired_path.replace(regex, "");

            // Elimina cualquier doble barra que pueda haber quedado
            new_url = new_url.replace("//", "/");

            // Si la URL termina con una barra, la eliminamos
            if (new_url.endsWith("/")) {
                new_url = new_url.slice(0, -1);
            }

            console.log("new_url", new_url);

            this.$inertia.visit(this.routeTo(new_url));
        },
    },
};
</script>
