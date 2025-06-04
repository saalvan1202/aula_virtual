<template>
    <section>
        <div class="row">
            <div class="col-md-12 col-lg-12">
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
                ||
                <Button ref="btnIndicator"  class="btn btn-sm btn-info" @click.native.prevent="btnShowIndicators">
                    <feather-icon
                        icon="CrosshairIcon"
                        class="mr-50"
                    />
                    <span class="align-middle">Indicadores</span>
                </Button>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-12">
                <Card>
                    <template #header>
                        <h4 class="card-title">
                            Administrar Competencias
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
            <template #title>Competencia</template>
            <form class="form form-vertical">
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Modulo</label>
                            <select class="form-control"
                                    v-model="form.id_padre"
                                    :class="{'is-invalid':errors.id_padre}"
                            >
                                <option value="0">SELECCIONE</option>
                                <option v-for="option in modulos" :key="option.id" :value="option.id">{{option.codigo}} - {{option.descripcion}}</option>
                            </select>
                            <InputError :error="errors.id_padre"/>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-lg-6">
                        <div class="form-group">
                            <label>Codigo</label>
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
                <div class="row">
                    <div class="col-md-12 col-lg-12">
                        <div class="form-group">
                            <label>Tipo Competencia</label>
                            <select class="form-control"
                                    v-model="form.id_componente"
                                    :class="{'is-invalid':errors.id_componente}"
                            >
                                <option value="0">SELECCIONE</option>
                                <option v-for="option in tipo_competencias" :key="option.id" :value="option.id">{{option.descripcion}}</option>
                            </select>
                            <InputError :error="errors.id_componente"/>
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
        <Indicator ref="indicator"/>
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
import Indicator from "./Indicator.vue";
export default{
    components: {Indicator, Card, DataTable, Modal,InputError,Button },
    name:'Competence',
    data(){
        return{
            columns:[
                { data: null, title: '#' ,orderable:false,searchable:false,render:function(data, type, full, meta){
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { data: 'modulo', title: 'Modulo',width:'5rem' },
                { data: 'codigo', title: 'Codigo',width:'5rem' },
                { data: 'descripcion', title: 'Competencia' },
            ],
            form:useForm({
                id:'-1',
                descripcion:'',
                codigo:'',
                id_componente:0,
                id_padre:0
            }),
            modulos:[]
        }
    },
    mounted() {
        this.loadModules();
    },
    methods:{
        loadModules(){
            this.$refs.btnCreate.load();
            this.$http.get(this.routeTo(`planes_estudios_modulos/${this.plan.id}/combos`)).then((res) => {
                this.modulos=res.data;
            }).catch((error) => {
                //console.log(error);
            }).finally(()=>{
                this.$refs.btnCreate.reset();
            })
        },
        setForm(data){
            this.setFormData(this.form,data);
        },
        reset(){
            this.form.reset();
            this.clearErrors('competence');
        },
        create(){
            this.reset();
            this.$refs.modal.show();
            //this.loadModules();
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
                    'X-Inertia-Error-Bag': 'competence'
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
        },
        btnShowIndicators(){
            const row=this.$refs.datatable.getSelectedRow();
            if(row==null){
                alertWarning('Seleccione un registro');
                return;
            }
            this.$refs.indicator.open(row);
        },
    },
    computed:{
        errors(){
            return this.$page.props.errors.competence ||{};
        },
        plan(){
            return this.$page.props.plan ||{};
        },
        urlRoute(){
            return 'competencias';
        },
        tipo_competencias(){
            return this.$page.props.tipo_competencias ||{};
        }
    }
}
</script>
