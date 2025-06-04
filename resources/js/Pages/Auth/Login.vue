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
                            Iniciar Sesión
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
                                <label class="d-block" for="login-email"
                                    >Usuario</label
                                >
                                <input
                                    type="text"
                                    v-model="form.usuario"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.usuario,
                                    }"
                                    id="login-email"
                                    v-focus
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.usuario"
                                    >{{ form.errors.usuario }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label class="d-block" for="login-password"
                                    >Contraseña</label
                                >
                                <div class="input-group input-group-merge">
                                    <input
                                        :type="passwordFieldType"
                                        v-model="form.password"
                                        class="form-control form-control-merge"
                                        :class="{
                                            'is-invalid': form.errors.password,
                                        }"
                                        id="login-password"
                                    />
                                    <div class="input-group-append">
                                        <div
                                            class="input-group-text"
                                            :class="{
                                                'text-invalid':
                                                    form.errors.password,
                                            }"
                                        >
                                            <feather-icon
                                                class="cursor-pointer"
                                                :icon="passwordToggleIcon"
                                                @click="
                                                    togglePasswordVisibility
                                                "
                                            />
                                        </div>
                                    </div>
                                    <small
                                        class="text-danger"
                                        style="width: 100%"
                                        v-if="form.errors.password"
                                        >{{ form.errors.password }}</small
                                    >
                                </div>
                            </div>
                            <div class="form-group" v-if="canResetPassword">
                                <div style="text-align: right">
                                    <Link :href="routeTo('password/reset')">
                                        <small>¿Olvidaste tu contraseña?</small>
                                    </Link>
                                </div>
                            </div>
                            <!-- <div
                                class="form-group"
                                style="padding-bottom: 1em"
                            ></div> -->
                            <button
                                class="btn btn-primary btn-block"
                                :disabled="form.processing"
                            >
                                <div
                                    v-if="form.processing"
                                    class="spinner-border spinner-border-sm"
                                ></div>
                                Ingresar
                            </button>
                            <button
                                class="btn btn-secondary btn-block"
                                @click.prevent="redirect('register')"
                            >
                                Registrarse
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
export default {
    name: "Login",
    components: { LayoutBlank, Link },
    props: {
        status: String,
        canResetPassword: Boolean,
    },
    data() {
        return {
            passwordFieldType: "password",
            form: useForm({
                usuario: "",
                password: "",
                remember: null,
            }),
        };
    },
    methods: {
        togglePasswordVisibility() {
            this.passwordFieldType =
                this.passwordFieldType === "password" ? "text" : "password";
        },
        submit() {
            this.form.post(this.routeTo("login"), {
                onFinish: () => this.form.reset("password"),
            });
        },
        redirect(name = "") {
            this.$inertia.visit(this.routeTo(`${name}`));
        },
    },
    computed: {
        passwordToggleIcon() {
            return this.passwordFieldType === "password"
                ? "EyeIcon"
                : "EyeOffIcon";
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
