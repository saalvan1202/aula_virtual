<template>
    <form>
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Contraseña actual</label>
                    <div class="input-group input-group-merge" :class="{'is-invalid':errors.current_password}">
                        <input class="form-control" :type="passwordFieldTypeCurrent" v-model="form.current_password" :class="{'is-invalid':errors.current_password}" />
                        <BInputGroupAppend is-text>
                            <feather-icon
                            :icon="passwordToggleIconCurrent"
                            class="cursor-pointer"
                            @click="togglePasswordCurrent"
                            />
                        </BInputGroupAppend>
                    </div>
                    <InputError :error="errors.current_password"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Contraseña nueva</label>
                    <div class="input-group input-group-merge" :class="{'is-invalid':errors.new_password}">
                        <input class="form-control" :type="passwordFieldTypeNew" v-model="form.new_password" :class="{'is-invalid':errors.new_password}" />
                        <BInputGroupAppend is-text>
                            <feather-icon
                            :icon="passwordToggleIconNew"
                            class="cursor-pointer"
                            @click="togglePasswordNew"
                            />
                        </BInputGroupAppend>
                    </div>
                    <InputError :error="errors.new_password"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Confirmar contraseña nueva</label>
                    <div class="input-group input-group-merge" :class="{'is-invalid':errors.new_password_confirmation}">
                        <input class="form-control" :type="passwordFieldTypeRetype" v-model="form.new_password_confirmation" :class="{'is-invalid':errors.new_password_confirmation}"/>
                        <BInputGroupAppend is-text>
                            <feather-icon
                            :icon="passwordToggleIconRetype"
                            class="cursor-pointer"
                            @click="togglePasswordRetype"
                            />
                        </BInputGroupAppend>
                    </div>
                    <InputError :error="errors.new_password_confirmation"/>
                    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <button class="btn btn-outline-success mr-1 mt-2"
                    @click.prevent="change"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="spinner-border spinner-border-sm"></span>
                    <feather-icon
                        v-else
                        icon="SaveIcon"
                        class="mr-50"
                    />
                    <span class="align-middle">Guardar Cambios</span>
                </button>
            </div>
        </div>
    </form>
</template>

<script>
import {BInputGroupAppend} from 'bootstrap-vue'
import InputError from '../../Components/InputError.vue';
import {alertSuccess} from '../../sweetAlert2.js';
export default {
    name: "AccountPassword",
    components:{
        BInputGroupAppend,
        InputError
    },
    data(){
        return{
            form:this.$inertia.form({
                current_password:'',
                new_password:'',
                new_password_confirmation:''
            }),
            passwordFieldTypeCurrent:'password',
            passwordFieldTypeNew:'password',
            passwordFieldTypeRetype:'password'
            
        }
    },
    methods:{
        change(){
            this.form.processing=true;
                this.$http({
                    method:'POST',
                    url: this.routeTo('usuarios/cuenta/clave'),
                    data: this.form.data(),
                    headers: {
                        'X-Inertia-Error-Bag': 'password'
                    }
                }).then((res) => {
                    alertSuccess('Datos Guardados Correctamente');
                    this.form.reset();

                }).catch((error) => {
                }).finally(()=>{
                    this.form.processing=false;

                })
        },

        togglePasswordCurrent() {
            this.passwordFieldTypeCurrent = this.passwordFieldTypeCurrent === 'password' ? 'text' : 'password'
        },
        togglePasswordNew() {
            this.passwordFieldTypeNew = this.passwordFieldTypeNew === 'password' ? 'text' : 'password'
        },
        togglePasswordRetype() {
            this.passwordFieldTypeRetype = this.passwordFieldTypeRetype === 'password' ? 'text' : 'password'
        },
    },
    computed: {
        passwordToggleIconCurrent() {
            return this.passwordFieldTypeCurrent === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        passwordToggleIconNew() {
            return this.passwordFieldTypeNew === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        passwordToggleIconRetype() {
            return this.passwordFieldTypeRetype === 'password' ? 'EyeIcon' : 'EyeOffIcon'
        },
        errors(){
            return this.$page.props.errors.password ||{};
        }
    },
}
</script>

<style scoped>

</style>
