<template>
    <LayoutBlank>
        <div class="auth-wrapper auth-v1 px-2">
            <div class="auth-inner py-2">
                <BCard class="mb-0">
                    <a class="brand-logo">
                        <img :src="$page.props.imagenes.logo" >
                        <h2 class="brand-text text-primary ml-1 m-2">
                            {{$page.props.app_name}}
                        </h2>
                    </a>
                    <div class="alert alert-warning" v-if="fail">
                        <div class="alert-body">
                            <strong>{{fail}}</strong>
                        </div>
                    </div>
                    <div class="alert alert-success" v-if="status">
                        <div class="alert-body">
                            <strong>{{status}}</strong>
                        </div>
                    </div>
                    <BCardTitle class="mb-1">
                        Restablecer  contraseña
                    </BCardTitle>
                    <BCardText class="mb-2">
                        Su nueva contraseña debe ser diferente de las contraseñas utilizadas anteriormente
                    </BCardText>
                    <BForm
                        class="auth-forgot-password-form mt-2"
                        @submit.prevent="submit"
                    >
                        <BFormGroup
                            label="Correo"
                            label-for="forgot-password-email"
                        >
                            <BFormInput
                                id="forgot-password-email"
                                :value="form.email"
                                name="forgot-password-email"
                                disabled
                                :class="{'is-invalid':form.errors.email}"
                            />
                            <small class="text-danger" v-if="form.errors.email">{{form.errors.email}}</small>
                        </BFormGroup>
                        <BFormGroup
                            label="Nueva Contraseña"
                            label-for="reset-password-new"
                        >
                            <BInputGroup
                                class="input-group-merge"
                                :class="{'is-invalid':form.errors.password}"
                            >
                                <BFormInput
                                    id="reset-password-new"
                                    v-model="form.password"
                                    name="reset-password-new"
                                    autofocus
                                    :type="password1FieldType"
                                    :class="{'is-invalid':form.errors.password}"
                                />
                                <BInputGroupAppend is-text>
                                    <feather-icon
                                        class="cursor-pointer"
                                        :icon="password1ToggleIcon"
                                        @click="togglePassword1Visibility"
                                    />
                                </BInputGroupAppend>
                            </BInputGroup>
                            <small class="text-danger" v-if="form.errors.password">{{form.errors.password}}</small>
                        </BFormGroup>
                        <BFormGroup
                            label="Confirmar Contraseña"
                            label-for="reset-password-confirm"
                        >
                            <BInputGroup
                                class="input-group-merge"
                                :class="{'is-invalid':form.errors.password_confirmation}"
                            >
                                <BFormInput
                                    id="reset-password-confirm"
                                    v-model="form.password_confirmation"
                                    name="reset-password-confirm"
                                    :type="password2FieldType"
                                    :class="{'is-invalid':form.errors.password_confirmation}"
                                    required
                                />
                                <BInputGroupAppend is-text>
                                    <feather-icon
                                        class="cursor-pointer"
                                        :icon="password2ToggleIcon"
                                        @click="togglePassword2Visibility"
                                    />
                                </BInputGroupAppend>
                            </BInputGroup>
                            <small class="text-danger" v-if="form.errors.password_confirmation">{{form.errors.password_confirmation}}</small>
                        </BFormGroup>
                        <BButton variant="primary" block type="submit" :disabled="form.processing">
                            <div v-if="form.processing" class="spinner-border spinner-border-sm"></div>
                            Restablecer Contraseña
                        </BButton>
                    </BForm>
                    <BCardText class="text-center mt-2">
                        <Link :href="routeTo('login')">
                            <feather-icon icon="ChevronLeftIcon" /> Regresar para inicio de sesión
                        </Link>
                    </BCardText>
                </BCard>
            </div>
        </div>
    </LayoutBlank>
</template>

<script>
import { useForm,Link } from '@inertiajs/vue2'
import LayoutBlank from "../../Layouts/LayoutBlank";
import {BCard,BCardTitle,BCardText,BForm,BFormGroup,BInputGroup,BFormInput,BInputGroupAppend,BButton} from 'bootstrap-vue'
export default {
    name: "ResetPassword",
    components: {
        LayoutBlank,BCard,BCardTitle,BCardText,BForm,
        BFormGroup,
        BInputGroup,
        BFormInput,
        BInputGroupAppend,
        BButton,Link
    },
    props:{
        fail:String,
        status:String,
        email:String,
        token:String,
    },
    data(){
        return{
            form:useForm({
                token: '',
                email: '',
                password: '',
                password_confirmation: '',

            }),
            // Toggle Password
            password1FieldType: 'password',
            password2FieldType: 'password',
        }
    },
    mounted(){
        this.form.email=this.email;
        this.form.token=this.token;
    },
    methods:{
        togglePassword1Visibility() {
            this.password1FieldType = this.password1FieldType === 'password' ? 'text' : 'password'
        },
        togglePassword2Visibility() {
            this.password2FieldType = this.password2FieldType === 'password' ? 'text' : 'password'
        },
        submit(){
            this.form.post(this.routeTo('password/reset'),{
                onFinish: () => {
                    this.form.reset('password', 'password_confirmation');
                },
            });
        }
    },
    computed:{
        password1ToggleIcon() {
            return this.password1FieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        password2ToggleIcon() {
            return this.password2FieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
    }
}
</script>

<style scoped>
/* color unsm #00793d */
body{
    background-color: #f8f8f8;
}
.auth-inner{
    background-color: #f8f8f8;
}
.brand-logo img{
    max-width: 100%;
    max-height: 60px;
}
.text-primary{
    color: #1A3882 !important;
}
.text-invalid{
    border-right: 1px solid #ea5455 !important;
    border-radius: 0 0.357rem 0.357rem 0 !important;
    border-left: transparent;
}
</style>
