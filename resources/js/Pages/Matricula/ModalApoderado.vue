<template>
    <Modal ref="modalApoderado" :loading="form.processing">
        <template #title>Apoderado</template>
        <form class="form form-vertical">
            <FormPerson :form="form" :errors="errors" />
            <div class="row">
                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label>Direccion</label>
                        <input class="form-control" type="text" v-model="form.direccion"
                               :class="{'is-invalid':errors.direccion}"
                               v-input-upper
                        />
                        <InputError :error="errors.direccion"/>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6">
                    <div class="form-group">
                        <label>Telefono</label>
                        <input class="form-control" type="text" v-model="form.telefono"
                               :class="{'is-invalid':errors.telefono}"
                               v-input-upper
                        />
                        <InputError :error="errors.telefono"/>
                    </div>
                </div>
            </div>
            <div class="row" style="display: none">
                <div class="col-md-12 col-lg-12">
                    <div class="form-group">
                        <label>Correo</label>
                        <input class="form-control" type="text" v-model="form.email"
                               :class="{'is-invalid':errors.email}"
                        />
                        <InputError :error="errors.email"/>
                    </div>
                </div>
            </div>
        </form>
        <template #footer>
            <button class="btn  btn-outline-danger" @click.prevent="close">
                <feather-icon
                    icon="XIcon"
                />
                <span>Cancelar</span>
            </button>
            <button class="btn btn-success" @click.prevent="store" :disabled="form.processing">
                <feather-icon
                    icon="SaveIcon"
                />
                <span>Guardar</span>
            </button>
        </template>
    </Modal>
</template>
<script>
import { useForm } from '@inertiajs/vue2'
import Modal from "../../Components/Modal.vue";
import Button from "../../Components/Button.vue";
import InputError from "../../Components/InputError.vue";
import FormPerson from "../../Components/FormPerson.vue";
import {alertSuccess} from "../../sweetAlert2";

export default{
    name:'ModalApoderado',
    components: {FormPerson, InputError, Button, Modal},
    data(){
        return{
            form:useForm({
                id:'-1',
                id_tipo_documento_identidad:1,
                numero_documento:'',
                nombres:'',
                apellido_paterno:'',
                apellido_materno:'',
                direccion:'',
                telefono:'',
                sexo:'M',
                estado_civil:'SOLTERO',
                id_personas:'-1'
            })
        }
    },
    methods:{
        setForm(data){
            this.setFormData(this.form,data);
        },
        create(){
            this.form.reset();
            this.$refs.modalApoderado.show();
        },
        close(){
            this.$refs.modalApoderado.hide();
        },
        store(){
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo(this.urlRoute),
                data: this.form.data(),
                headers: {
                    "X-Inertia-Error-Bag": "apoderado",
                },
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');
                //this.$refs.datatable.reload();
                this.$refs.modalApoderado.hide();
                this.$emit('store',res.data);

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        }
    },
    computed:{
        errors(){
            return this.$page.props.errors.apoderado ||{};
        },
        urlRoute(){
            return 'apoderados';
        }
    }

}
</script>
<style scoped>

</style>
