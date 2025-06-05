<template>
    <LayoutBlank>
        <div class="auth-wrapper auth-v2">
            <div class="row auth-inner m-0">
                <!-- Brand logo-->
                <a class="brand-logo">
                    <img :src="$page.props.imagenes.logo" />
                    <h2 class="brand-text text-primary ml-1 m-2">
                        {{ $page.props.app_name }}
                    </h2>
                </a>
                <!-- /Brand logo-->
                <!-- Left Text-->
                <div class="col-lg-8 d-none d-lg-flex align-items-center p-5">
                    <div
                        class="w-100 d-lg-flex align-items-center justify-content-center px-5"
                    >
                        <img
                            :src="$page.props.imagenes.banner"
                            class="img-fluid"
                        />
                    </div>
                </div>
                <!-- /Left Text-->
                <!-- Login-->
                <div
                    class="col-lg-4 d-flex align-items-center auth-bg px-2 p-lg-3"
                >
                    <div class="col-sm-8 col-md-6 col-lg-12 px-xl-2 mx-auto">
                        <h2
                            class="card-title font-weight-bold mb-2"
                            style="color: red"
                        >
                            Error de token
                        </h2>
                        <BAlert
                            v-height-fade
                            show
                            dismissible
                            fade
                            class="mb-0"
                            variant="success"
                            v-if="status"
                        >
                            <div class="alert-body">
                                <strong>{{ status }}</strong>
                            </div>
                        </BAlert>
                        <p>{{ errors_token.token }}</p>
                        <button
                            class="btn btn-primary btn-block"
                            @click="redirect('/')"
                        >
                            Iniciar sesión
                        </button>
                        <button
                            class="btn btn-secondary btn-block mt-1"
                            @click="redirect('register')"
                        >
                            Registrarse
                        </button>
                    </div>
                </div>
                <!-- /Login-->
            </div>
        </div>
    </LayoutBlank>
</template>

<script>
import { useForm, Link } from "@inertiajs/vue2";
import LayoutBlank from "../../Layouts/LayoutBlank";
import {
    alertSuccess,
    alertWarning,
    confirm,
    alertError,
} from "../../sweetAlert2.js";
export default {
    name: "Register",
    components: { LayoutBlank, Link },
    props: {
        status: String,
        canResetPassword: Boolean,
    },
    data() {
        return {
            form: useForm({
                dni: "",
                email: "",
            }),
            isSubmitting: false,
            errors: {},
        };
    },
    methods: {
        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${name}`));
        },
    },
    computed: {
        errors_token() {
            console.log(this.$page.props.errors);
            return this.$page.props.errors || "Se produjo un error inesperado.";
        },
    },
};
</script>

<style scoped>
/* color unsm #00793d */
body {
    background-color: #f8f8f8;
}
.auth-inner {
    background-color: #f8f8f8;
}
.brand-logo img {
    max-width: 100%;
    max-height: 60px;
}
.text-primary {
    color: #1a3882 !important;
}
.text-invalid {
    border-right: 1px solid #ea5455 !important;
    border-radius: 0 0.357rem 0.357rem 0 !important;
    border-left: transparent;
}
</style>
