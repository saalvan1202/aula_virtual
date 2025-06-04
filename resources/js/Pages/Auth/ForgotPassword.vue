<template>
    <LayoutBlank>
        <div class="auth-wrapper auth-v1 px-2">
            <div class="auth-inner py-2">
                <BCard class="mb-0">
                    <a class="brand-logo">
                        <img :src="$page.props.imagenes.logo" >
                        <!--<h2 class="brand-text text-primary ml-1 m-2">
                            {{$page.props.app_name}}
                        </h2>-->
                    </a>
                    <div class="alert alert-warning" v-if="fail">
                        <div class="alert-body">
                            <strong>{{fail}}</strong>
                        </div>
                    </div>
                    <div class="alert alert-success" v-else-if="status">
                        <div class="alert-body">
                            <strong>{{status}}</strong>
                        </div>
                    </div>
                    <BCardTitle class="mb-1">
                        Olvidé mi contraseña
                    </BCardTitle>
                    <BCardText class="mb-2">
                        Ingresa tu correo electrónico y te enviaremos instrucciones para restablecer tu contraseña
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
                                v-model="form.email"
                                name="forgot-password-email"
                                autofocus
                                :class="{'is-invalid':form.errors.email}"
                                autocomplete="off"
                            />
                            <small class="text-danger" v-if="form.errors.email">{{form.errors.email}}</small>
                        </BFormGroup>
                        <BButton variant="primary" block type="submit" :disabled="form.processing">
                            <div v-if="form.processing" class="spinner-border spinner-border-sm"></div>
                            Enviar
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
import {BCard,BCardTitle,BCardText,BForm,BFormGroup,BFormInput,BButton} from 'bootstrap-vue'
export default {
    name: "ForgotPassword",
    components: {LayoutBlank,BCard,BCardTitle,BCardText,BForm,BFormGroup,BFormInput,BButton,Link},
    props:{
        fail:String,
        status:String
    },
    data(){
        return{
            form:useForm({
                email:''
            })
        }
    },
    methods:{
        submit(){
            this.form.post(this.routeTo('password/email'),{
                //onFinish: () => this.form.reset('email'),
            });
        }
    },
    computed:{

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
