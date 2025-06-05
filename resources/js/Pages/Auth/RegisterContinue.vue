<template>
    <LayoutBlank>
        <div class="auth-wrapper auth-v2">
            <div class="row auth-inner m-0">
                <!-- Brand logo-->
                <a class="brand-logo d-none d-md-block">
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
                    <div
                        class="col-sm-8 col-md-6 col-lg-12 px-xl-2 mt-3 mx-auto"
                    >
                        <h2 class="card-title font-weight-bold mb-2">
                            Datos personales
                        </h2>
                        <form
                            class="auth-login-form mt-2"
                            @submit.prevent="submit"
                        >
                            <div class="form-group">
                                <label class="d-block" for="validated-nombres"
                                    >Nombre(s)</label
                                >
                                <input
                                    type="text"
                                    v-model="form.nombres"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.nombres,
                                    }"
                                    id="validated-nombres"
                                    autofocus
                                    style="text-transform: uppercase"
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.nombres"
                                    >{{ form.errors.nombres }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label
                                    class="d-block"
                                    for="validated-apellido_paterno"
                                    >Apellido Paterno</label
                                >
                                <input
                                    type="text"
                                    v-model="form.apellido_paterno"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            form.errors.apellido_paterno,
                                    }"
                                    id="validated-apellido_paterno"
                                    autofocus
                                    style="text-transform: uppercase"
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.apellido_paterno"
                                    >{{ form.errors.apellido_paterno }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label
                                    class="d-block"
                                    for="validated-apellido_materno"
                                    >Apellido Materno</label
                                >
                                <input
                                    type="text"
                                    v-model="form.apellido_materno"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            form.errors.apellido_materno,
                                    }"
                                    id="validated-apellido_materno"
                                    autofocus
                                    style="text-transform: uppercase"
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.apellido_materno"
                                    >{{ form.errors.apellido_materno }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label class="d-block" for="validated-sexo"
                                    >Sexo</label
                                >
                                <select
                                    v-model="form.sexo"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.sexo,
                                    }"
                                    id="validated-sexo"
                                >
                                    <option value="0" disabled>
                                        Seleccione una opción
                                    </option>
                                    <option value="M">MASCULINO</option>
                                    <option value="F">FEMENINO</option>
                                </select>
                                <small
                                    class="text-danger"
                                    v-if="form.errors.sexo"
                                    >{{ form.errors.sexo }}</small
                                >
                            </div>
                            <h2 class="card-title font-weight-bold my-2">
                                Datos de usuario
                            </h2>
                            <div class="form-group">
                                <label class="d-block" for="validated-email"
                                    >Correo</label
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
                                    disabled
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.email"
                                    >{{ form.errors.email }}</small
                                >
                            </div>
                            <div class="form-group">
                                <label class="d-block" for="validated-usuario"
                                    >Usuario</label
                                >
                                <input
                                    type="text"
                                    v-model="form.usuario"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.usuario,
                                    }"
                                    id="validated-usuario"
                                    autofocus
                                    disabled
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
                            <div class="form-group">
                                <label
                                    class="d-block"
                                    for="login-confirm-password"
                                    >Confirmar contraseña</label
                                >
                                <div class="input-group input-group-merge">
                                    <input
                                        :type="passwordFieldType"
                                        v-model="form.password_confirmation"
                                        class="form-control form-control-merge"
                                        :class="{
                                            'is-invalid':
                                                form.errors
                                                    .password_confirmation,
                                        }"
                                        id="login-password_confirmation"
                                        @paste.prevent
                                        @contextmenu.prevent
                                        @drop.prevent
                                        @dragover.prevent
                                    />
                                    <div class="input-group-append">
                                        <div
                                            class="input-group-text"
                                            :class="{
                                                'text-invalid':
                                                    form.errors
                                                        .password_confirmation,
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
                                        v-if="form.errors.password_confirmation"
                                        >{{
                                            form.errors.password_confirmation
                                        }}</small
                                    >
                                </div>
                            </div>

                            <button
                                class="btn btn-primary btn-block mb-3"
                                type="submit"
                            >
                                Completar registro
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
    name: "Register",
    components: { LayoutBlank, Link },
    props: {
        status: String,
        canResetPassword: Boolean,
        user: Object,
        token: String,
    },
    data() {
        return {
            form: useForm({
                nombres: "",
                apellido_paterno: "",
                apellido_materno: "",
                sexo: "",
                usuario: "",
                email: "",
                password: "",
                password_confirmation: "",
            }),
            passwordFieldType: "password",
        };
    },
    methods: {
        togglePasswordVisibility() {
            this.passwordFieldType =
                this.passwordFieldType === "password" ? "text" : "password";
        },
        isPasswordStrong(password) {
            const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;
            return regex.test(password);
        },
        submit() {
            console.log("sadasdas");
            // Validar fortaleza de contraseña
            if (!this.isPasswordStrong(this.form.password)) {
                this.form.errors.password =
                    "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
                return;
            }

            // Validar confirmación de contraseña
            if (this.form.password !== this.form.password_confirmation) {
                this.form.errors.password_confirmation =
                    "Las contraseñas no coinciden.";
                return;
            }

            // Limpiar errores previos si pasa validación
            this.form.clearErrors();

            const {
                usuario,
                email,
                nombres,
                apellido_paterno,
                apellido_materno,
                ...data
            } = this.form.data();

            const new_data = {
                ...data,
                nombres: this.form.nombres.toUpperCase(),
                apellido_paterno: this.form.apellido_paterno.toUpperCase(),
                apellido_materno: this.form.apellido_materno.toUpperCase(),
            };

            this.$http
                .post(
                    this.routeTo(`finalizar-registro/${this.token}`),
                    new_data
                )
                .then((response) => {
                    alertSuccess("Enlace enviado");
                    this.form.reset();
                })
                .catch((error) => {
                    // Manejar errores
                });
        },
    },
    mounted() {
        // Asignar datos del usuario si están disponibles
        if (this.user) {
            this.form.nombres = this.user.persona.nombres || "";
            this.form.apellido_paterno =
                this.user.persona.apellido_paterno || "";
            this.form.apellido_materno =
                this.user.persona.apellido_materno || "";
            this.form.sexo = this.user.persona.sexo || "";
            this.form.email = this.user.email || "";
            this.form.usuario = this.user.usuario || "";
        }
    },
    computed: {
        passwordToggleIcon() {
            return this.passwordFieldType === "password"
                ? "EyeIcon"
                : "EyeOffIcon";
        },
    },
    watch: {
        "form.password"(val) {
            // Validar fortaleza de la contraseña mientras escribe
            if (!this.isPasswordStrong(val)) {
                this.form.errors.password =
                    "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
            } else {
                this.form.errors.password = null;
            }

            // Validar si coincide con confirmación (si ya hay algo escrito)
            if (
                this.form.password_confirmation !== "" &&
                val !== this.form.password_confirmation
            ) {
                this.form.errors.password_confirmation =
                    "Las contraseñas no coinciden.";
            } else {
                this.form.errors.password_confirmation = null;
            }
        },

        "form.password_confirmation"(val) {
            // Validar coincidencia en tiempo real
            if (val !== this.form.password) {
                this.form.errors.password_confirmation =
                    "Las contraseñas no coinciden.";
            } else {
                this.form.errors.password_confirmation = null;
            }
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
