<template>
    <form >
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="form-group">
                    <label>Nombres</label>
                    <input class="form-control" type="text" v-model="form.nombres"
                        :class="{'is-invalid':errors.nombres}"
                        v-input-upper
                    />
                    <InputError :error="errors.nombres"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Apellido Paterno</label>
                    <input class="form-control" type="text"
                        v-model="form.apellido_paterno"
                        :class="{'is-invalid':errors.nombres}"
                        v-input-upper
                    />
                    <InputError :error="errors.apellido_paterno"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Apellido Materno</label>
                    <input class="form-control" type="text"
                        v-model="form.apellido_materno"
                        :class="{'is-invalid':errors.apellido_materno}"
                        v-input-upper
                    />
                     <InputError :error="errors.apellido_materno"/>
                </div>
            </div>
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
import InputError from '../../Components/InputError.vue';
import {alertSuccess} from '../../sweetAlert2.js';
export default {
    components: { InputError },
    name: "AccountGeneral",
    props:{
        user:Object
    },
    data(){
        return {
            form:this.$inertia.form({
                nombres:this.user.persona.nombres,
                apellido_paterno:this.user.persona.apellido_paterno,
                apellido_materno:this.user.persona.apellido_materno
            })
        }
    },
    methods:{
        change(){
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo('usuarios/cuenta/cambiar'),
                data: this.form.data(),
                headers: {
                    'X-Inertia-Error-Bag': 'account'
                }
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        }
    },
    computed:{
        errors(){
            return this.$page.props.errors.account ||{};
        }
    }
}
</script>

<style scoped>

</style>
