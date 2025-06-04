<template>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <button  class="btn btn-sm btn-primary" @click.prevent="create">
                        <feather-icon
                        icon="PlusIcon"
                        class="mr-50"
                    />
                    <span class="align-middle">Nuevo</span>
                    </button>
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
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                         <template #header>
                            <h4 class="card-title">
                                Administrar Modulos
                            </h4>
                        </template>
                        <DataTable
                            :height="200"
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/${this.plan.id}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
            <Modal ref="modal" :loading="form.processing">
                <template #title>Modulo</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Numero</label>
                                <input class="form-control" type="text" v-model="form.codigo"
                                    :class="{'is-invalid':errors.codigo}"

                                />
                                <InputError :error="errors.codigo"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Descripcion</label>
                                <input class="form-control" type="text" v-model="form.descripcion"
                                    v-input-upper
                                    :class="{'is-invalid':errors.descripcion}"

                                />
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
        </section>
</template>
<script>
import { useForm } from '@inertiajs/vue2'
import Card from '../../../Components/Card.vue'
import DataTable from '../../../Components/DataTable.vue'
import Modal from '../../../Components/Modal.vue'
import InputError from '../../../Components/InputError.vue';
import Button from '../../../Components/Button.vue';
import {alertSuccess, alertWarning,confirm} from '../../../sweetAlert2';
export default{
    components: {Card, DataTable, Modal,InputError,Button },
    name:'Module',
    data(){
        return{
            columns:[
                { data: null, title: '#' ,orderable:false,searchable:false,render:function(data, type, full, meta){
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { data: 'codigo', title: 'Numero',width:'5rem' },
                { data: 'descripcion', title: 'Modulo' },
            ],
            form:useForm({
                id:'-1',
                descripcion:'',
                codigo:'',
                id_plan_estudio:0
            }),
            parents:[]
        }
    },
    mounted() {
    },
    methods:{
        setForm(data){
            this.setFormData(this.form,data);
        },
        reset(){
            this.form.reset();
            this.clearErrors('modules');
        },
        create(){
            this.reset();
            this.$refs.modal.show();
        },
        close(){
            this.$refs.modal.hide();
        },
        store(){
            this.form.id_plan_estudio=this.plan.id;
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
                headers: {
                    'X-Inertia-Error-Bag': 'modules'
                }
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');
                this.$refs.datatable.reload();
                this.$refs.modal.hide();

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        },
        edit(){
            const row=this.$refs.datatable.getSelectedRow();
            if(row==null){
                alertWarning('Seleccione un registro');
                return;
            }
            this.reset();
            this.$refs.btnEdit.load();
            this.$http.get(this.routeTo(`${this.urlRoute}/${row.id}/edit`)).then((res) => {
                this.setForm(res.data);
                this.$refs.modal.show();
            }).catch((error) => {
                //console.log(error);
            }).finally(()=>{
                this.$refs.btnEdit.reset();
            })
        },
        destroy(){
            const row=this.$refs.datatable.getSelectedRow();
            if(row==null){
                alertWarning('Seleccione un registro');
                return;
            }
            confirm({
                text:'Desea eliminar el registro seleccionado?'
            },()=>{
                this.$refs.btnDestroy.load();
                this.$http.delete(this.routeTo(`${this.urlRoute}/${row.id}`)).then((res) => {
                    alertSuccess('Eliminado Correctamente');
                     this.$refs.datatable.reload();
                }).catch((error) => {

                }).finally(()=>{
                    this.$refs.btnDestroy.reset();
                });
            });
        }
    },
    computed:{
        errors(){
            return this.$page.props.errors.modules ||{};
        },
        plan(){
            return this.$page.props.plan ||{};
        },
        urlRoute(){
            return 'planes_estudios_modulos';
        }
    }
}
</script>
