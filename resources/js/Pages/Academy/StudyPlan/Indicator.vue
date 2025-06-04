<template>
    <Fragment>
        <Modal ref="modalI" modal="modal-lg">
            <template #title>Indicadores de logro</template>
            <div class="row">
                <div class="col">
                    <Button ref="btnCreate" class="btn btn-sm btn-primary" @click.native.prevent="create">
                        <feather-icon
                            icon="PlusIcon"
                            class="mr-50"
                        />
                        <span class="align-middle">Nuevo</span>
                    </Button>
                    <Button ref="btnEdit"  class="btn btn-sm btn-warning" @click.native.prevent="edit">
                        <feather-icon
                            icon="EditIcon"
                            class="mr-50"
                        />
                        <span class="align-middle">Modificar</span>
                    </Button>
                    <Button ref="btnDestroy" class="btn btn-sm btn-danger" @click.native.prevent="destroy">
                        <feather-icon
                            icon="Trash2Icon"
                            class="mr-50"
                        />
                        <span class="align-middle">Eliminar</span>
                    </Button>
                </div>
                <div class="col-md-12 col-lg-12">
                    <DataTable
                        ref="datatableI"
                        :columns="columns"
                        :path="routeTo(urlDatatable)"
                    />
                </div>
            </div>
        </Modal>
        <Modal ref="modalC" :loading="form.processing">
            <template #title>Indicadores de logro</template>
            <form class="form form-vertical">
                <div class="row">
                    <div class="col-md-3 col-lg-3">
                        <div class="form-group">
                            <label>Numero</label>
                            <input class="form-control" type="number" v-model="form.numero"
                                   :class="{'is-invalid':errors.numero}"

                            />
                            <InputError :error="errors.numero"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Descripcion</label>
                            <textarea class="form-control" rows="3" v-input-upper  v-model="form.descripcion"
                                :class="{'is-invalid':errors.descripcion}"
                            >
                            </textarea>
                            <InputError :error="errors.descripcion"/>
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
    </Fragment>
</template>
<script>
import Vue from 'vue';
import { useForm } from '@inertiajs/vue2'
import { Fragment } from 'vue-fragment'
import Modal from "../../../Components/Modal.vue";
import DataTable from "../../../Components/DataTable.vue";
import Button from "../../../Components/Button.vue";
import InputError from "../../../Components/InputError.vue";
import {alertSuccess} from "../../../sweetAlert2";
import {routeTo} from "../../../helpers";
export default{
    name:'Indicator',
    components: {InputError, Button, DataTable, Fragment,Modal},
    data(){
        return {
            columns:[
                { data: null, title: '#' ,orderable:false,searchable:false,render:function(data, type, full, meta){
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    },width:'3rem'
                },
                //{ data: 'numero', title: 'Numero',width:'5rem' },
                { data: 'descripcion', title: 'Indicador' },
            ],
            competencia:{
                id:0,
                modulo:'',
                descripcion:''
            },
            form:useForm({
                id:'-1',
                id_modulo_competencia:0,
                numero:'',
                descripcion:'',
            })
        }
    },
    methods:{
        open(competencia){
            Vue.set(this.$data,'competencia',competencia);

            this.$nextTick(()=>{
                this.$refs.datatableI.url(routeTo(this.urlDatatable))
                this.$refs.modalI.show();
            });
        },
        setForm(data){
            this.setFormData(this.form,data);
        },
        reset(){
            this.form.reset();
            this.clearErrors('indicators');
        },
        create(){
            this.reset();
            this.$refs.modalC.show();
        },
        close(){
            this.$refs.modalC.hide();
        },
        store(){
            this.form.id_modulo_competencia=this.competencia.id;
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
                headers: {
                    'X-Inertia-Error-Bag': 'indicators'
                }
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');
                this.$refs.datatableI.reload();
                this.$refs.modalC.hide();

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        },
        edit(){
            this.reset();
        },
        destroy(){

        }
    },
    computed:{
        urlRoute(){
            return 'indicadores_competencias';
        },
        errors(){
            return this.$page.props.errors.indicators ||{};
        },
        urlDatatable(){
            return `${this.urlRoute}/${this.competencia.id}/datatables`;
        }
    }
}
</script>



<style scoped>

</style>
