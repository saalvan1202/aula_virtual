<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                    <button  class="btn btn-primary" @click.prevent="create">
                        <feather-icon
                            icon="PlusIcon"
                            class="mr-50"
                        />
                        <span class="align-middle">Nuevo</span>
                    </button>
                    <Button ref="btnEdit"  class="btn btn-warning" @click.native.prevent="edit">
                        <feather-icon
                            icon="EditIcon"
                            class="mr-50"
                        />
                        <span class="align-middle">Modificar</span>
                    </Button>
                    <Button ref="btnDestroy" class="btn btn-danger" @click.native.prevent="destroy">
                        <feather-icon
                            icon="Trash2Icon"
                            class="mr-50"
                        />
                        <span class="align-middle">Eliminar</span>
                    </Button>
                    ||
                    <Button ref="btnPrint" class="btn btn-outline-dark" @click.native.prevent="onPrint">
                        <feather-icon
                            icon="PrintIcon"
                            class="mr-50"
                        />
                        <span class="align-middle">Imprimir</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                Administrar Certificado Modular
                            </h4>
                        </template>
                        <DataTable
                            ref="datatable"
                            :columns="columns"
                            :path="routeTo(`${this.urlRoute}/datatables`)"
                        />
                    </Card>
                </div>
            </div>
            <Modal ref="modal" :loading="form.processing">
                <template #title>Certificado</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectStudent
                                ref="estudiante"
                                :errors="errors.id_estudiante"
                                @input="onStudent"
                                :disabled="form.id!='-1'"
                            />
                        </div>
                    </div>
                    <div class="row" v-if="mostrar_mensaje">
                        <div class="col-md-12">
                            <div class="alert alert-warning">
                                <div class="alert-body">
                                    NO TIENES MODULOS PARA CERTIFICAR
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" v-if="!mostrar_mensaje">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Modulos</label>
                                <select class="form-control"
                                        v-model="form.id_referencia"
                                        :class="{'is-invalid':errors.id_referencia}"
                                >
                                    <option value=0>SELECCIONE</option>
                                    <option v-for="option in modulos" :key="option.id" :value="option.id">{{option.descripcion}}[{{option.estado}}]</option>
                                </select>
                                <InputError :error="errors.id_referencia"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Fecha Inicio</label>
                                <input class="form-control" type="date" v-model="form.fecha_inicio"
                                    :class="{'is-invalid':errors.fecha_inicio}"
                                />
                                <InputError :error="errors.fecha_inicio"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Fecha Fin</label>
                                <input class="form-control" type="date" v-model="form.fecha_fin"
                                    :class="{'is-invalid':errors.fecha_fin}"
                                />
                                <InputError :error="errors.fecha_fin"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Numero de Registro Institucional</label>
                                <input class="form-control" type="text" v-model="form.numero_registro_institucional"
                                    maxlength="70"
                                    v-input-upper
                                    :class="{'is-invalid':errors.numero_registro_institucional}"
                                />
                                <InputError :error="errors.numero_registro_institucional"/>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="display: none">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Director General</label>
                                <input class="form-control" placeholder="Apellidos y nombres" type="text" v-model="form.director_general"
                                    maxlength="150"
                                    v-input-upper
                                    :class="{'is-invalid':errors.director_general}"
                                />
                                <InputError :error="errors.director_general"/>
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
    </LayoutContent>
</template>
<script>
import { useForm } from '@inertiajs/vue2'
import LayoutContent from '../../Layouts/LayoutContent.vue'
import Card from '../../Components/Card.vue'
import DataTable from '../../Components/DataTable.vue'
import Modal from '../../Components/Modal.vue'
import InputError from '../../Components/InputError.vue';
import Button from '../../Components/Button.vue';
import {alertSuccess, alertWarning,confirm} from '../../sweetAlert2.js';
import SelectStudent from "../Matricula/Components/SelectStudent.vue";
import {printIframe} from "../../helpers";
export default{
    components: {SelectStudent, LayoutContent, Card, DataTable, Modal,InputError,Button },
    name:'Modulate',
    props:{
        urlRoute:String
    },
    data(){
        return{
            columns:[
                { data: null, title: '#' ,orderable:false,searchable:false,render:function(data, type, full, meta){
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { data: 'fecha', title: 'Fecha' },
                { data: 'estudiante', title: 'Estudiante' },
                { data: 'modulo', title: 'Modulo' },

            ],
            form:useForm({
                id:'-1',
                id_estudiante:0,
                id_referencia:0,
                fecha_inicio:'',
                fecha_fin:'',
                numero_registro_institucional:'',
                //director_general:''
            }),
            mostrar_mensaje:false,
            modulos:[]
        }
    },
    mounted() {

    },
    methods:{
        onStudent(estudiante){
            if(estudiante.id==undefined){
                return;
            }
            this.form.id_estudiante=estudiante.id;
            this.mostrar_mensaje=false;
            this.$http.get(this.routeTo(`${this.urlRoute}/modulos?id_estudiante=${estudiante.id}&id_plan_estudio=${estudiante.id_plan_estudio}`)).then((res) => {
                this.modulos=res.data;
                if(res.data.length==0){
                    this.mostrar_mensaje=true;
                }
            }).catch((error) => {
                //console.log(error);
            }).finally(()=>{
            })
            //aca traemos los modulos
        },
        setForm(data){
            if(data.estudiante){
                this.$refs.estudiante.setData(data.estudiante);
            }
            this.$nextTick(()=>{
                this.setFormData(this.form,data);
            })

        },
        reset(){
            this.form.reset();
            this.$refs.estudiante.clean();
            this.clearErrors();
        },
        create(){
            this.reset();
            this.$refs.modal.show();
        },
        close(){
            this.$refs.modal.hide();
        },
        findModule(){
            return this.modulos.find((el)=>el.id=this.form.id_referencia);
        },
        store(){
            //validaremos que solo los aprobados se puedan certificar
            const module=this.findModule();
            if(module!=undefined && module.estado!='CONCLUIDO'){
                alertWarning('solo puedes generar certificado modular  de modulos concluidos');
                return;
            }
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data(),
            }).then((res) => {
                alertSuccess('Certificado Generado Correctamente');
                this.$refs.datatable.reload();
                this.$refs.modal.hide();
                this.confirm(res.data);

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        },
        confirm(response){
            confirm({
                confirmButtonText:'Si',
                cancelButtonText:'No'
            },()=>{
                printIframe(`${this.urlRoute}/${response.id}/print`);
            });
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
        onPrint(){
            const row=this.$refs.datatable.getSelectedRow();
            if(row==null){
                alertWarning('Seleccione un registro');
                return;
            }
            this.$refs.btnPrint.load();
            printIframe(`${this.urlRoute}/${row.id}/print`,()=>{
                this.$refs.btnPrint.reset();
            });
        }
    },
    computed:{
        errors(){
            return this.$page.props.errors ||{};
        }
    }
}
</script>
