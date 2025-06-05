<template>
    <LayoutBlank>
        <div class="auth-wrapper auth-v2">
            <div class="row auth-inner m-0">
                <div
                    class="col-lg-4 d-flex align-items-center auth-bg px-2 p-lg-3"
                >
                    <div class="col-12 px-xl-2 mx-auto">
                        <h2 class="card-title font-weight-bold mb-2">
                            Verificar OTP
                        </h2>
                        <form @submit.prevent="submit">
                            <div class="form-group">
                                <label>Código OTP</label>
                                <input
                                    v-model="form.otp_code"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': form.errors.otp_code,
                                    }"
                                />
                                <small
                                    class="text-danger"
                                    v-if="form.errors.otp_code"
                                >
                                    {{ form.errors.otp_code }}
                                </small>
                            </div>
                            <button
                                class="btn btn-primary btn-block"
                                :disabled="form.processing"
                            >
                                Verificar
                            </button>
                        </form>
                    </div>
                </div>
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
            </div>
        </div>
    </LayoutBlank>
</template>

<script>
import LayoutBlank from "../../Layouts/LayoutBlank";
import { useForm } from "@inertiajs/vue2";

export default {
    name: "OtpVerify",
    components: { LayoutBlank },
    data() {
        return {
            form: useForm({
                otp_code: "",
            }),
        };
    },
    methods: {
        submit() {
            this.$http
                .post(this.routeTo("otp-verify"), {
                    otp_code: this.form.otp_code,
                })
                .then((response) => {
                    this.$inertia.visit(this.routeTo("/"));
                })
                .catch((error) => {
                    console.log(error.response);
                })
                .finally(() => {
                    window.location.reload(); // Uncomment if you want to force a reload
                });
        },
    },
};
</script>
