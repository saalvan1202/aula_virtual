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
                        <h2 class="card-title font-weight-bold mb-2">
                            Registrarse
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
                        <form
                            class="auth-login-form mt-2"
                            @submit.prevent="submit"
                        >
                            <div class="form-group">
                                <label class="d-block" for="validated-dni"
                                    >DNI
                                    <span style="color: red; margin-left: 2px">
                                        *
                                    </span></label
                                >
                                <input
                                    type="text"
                                    v-model="form.dni"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.dni,
                                    }"
                                    id="validated-dni"
                                    autofocus
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.dni"
                                    >{{ form.errors.dni }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label class="d-block" for="validated-email"
                                    >Correo
                                    <span style="color: red; margin-left: 2px">
                                        *
                                    </span></label
                                >
                                <input
                                    type="text"
                                    v-model="form.email"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.email,
                                    }"
                                    id="validated-email"
                                    v-focus
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.email"
                                    >{{ form.errors.email }}</small
                                >
                            </div>

                            <button
                                class="btn btn-primary btn-block"
                                type="submit"
                                :disabled="
                                    isSubmitting || !form.dni || !form.email
                                "
                            >
                                <span
                                    v-if="isSubmitting"
                                    class="spinner-border spinner-border-sm"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                Enviar enlace
                            </button>
                        </form>
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
        };
    },
    methods: {
        submit() {
            this.isSubmitting = true;

            this.$http
                .post(this.routeTo("registro/verificar"), {
                    dni: this.form.dni,
                    email: this.form.email,
                })
                .then((response) => {
                    alertSuccess(response.data.message || "Enlace enviado");
                    this.form.reset();
                })
                .catch((error) => {
                    alertError(
                        error.response.data.message ||
                            "Ocurrió un error al enviar el enlace."
                    );
                })
                .finally(() => {
                    this.isSubmitting = false;
                });
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
