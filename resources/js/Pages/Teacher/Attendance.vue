<template>
    <LayoutContent>
        <div class="row mt-1">
            <div class="col-12">
                <Card>
                    <Spinner v-if="form.processing"/>
                    <template #header>
                        <h4 class="card-title">
                            Control Asistencia Docentes
                        </h4>
                    </template>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label class="form-label">Periodo Clases</label>
                                <select class="form-control"
                                        v-model="form.id_periodo_clases"
                                        :class="{'is-invalid':errors.id_periodo_clases}"
                                >
                                    <option value='0'>SELECCIONE</option>
                                    <option v-for="option in periodo_clases" :key="option.id" :value="option.id">{{option.descripcion}}</option>
                                </select>
                                <InputError :error="errors.id_periodo_clases"/>
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4">
                            <div class="form-group">
                                <label>Fecha</label>
                                <input class="form-control" type="date" v-model="form.fecha"
                                       :class="{'is-invalid':errors.fecha}">
                                <InputError :error="errors.fecha"/>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <Button ref="btnShow" class="btn btn-primary" style="display: block" @click.native.prevent="show">Mostrar</Button>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="offset-md-9 col-md-3">
                            <div class="form-group mb-sm">
                                <label>Seleccionar Para Todos</label>
                                <select class="form-control" v-model="todos" @change="onTodo">
                                    <option value="">NO SELECCIONADO</option>
                                    <option value="PRESENTE">PRESENTE</option>
                                    <option value="FALTA">FALTA</option>
                                    <option value="TARDE">TARDE</option>
                                    <option value="JUSTIFICADO">JUSTIFICADO</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive" style="height: 380px;overflow-y:auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:3rem ;">#</th>
                                    <th>Apellidos Y Nombres</th>
                                    <th>Documento</th>
                                    <th>Numero</th>
                                    <th title="Presente" style="width:3rem ;">A</th>
                                    <th title="Falta" style="width:3rem ;">F</th>
                                    <th title="Tarde" style="width:3rem ;">T</th>
                                    <th title="Justificado" style="width:3rem ;">J</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in form.detalle">
                                    <td>{{index+1}}</td>
                                    <td>{{item.docente}}</td>
                                    <td>{{item.tipo_documento}}</td>
                                    <td>{{item.numero_documento}}</td>
                                    <td><input type="radio" value="PRESENTE" v-model="item.tipo_asistencia"></td>
                                    <td><input type="radio" value="FALTA" v-model="item.tipo_asistencia"></td>
                                    <td><input type="radio" value="TARDE" v-model="item.tipo_asistencia"></td>
                                    <td>
                                        <input type="radio"  value="JUSTIFICADO" v-model="item.tipo_asistencia">

                                    </td>
                                    <td><input class="form-control input-group-sm" maxlength="100" v-model="item.observaciones"  v-input-upper/></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="col text-right">
                            <button class="btn btn-outline-danger">Cancelar</button>
                            <Button class="btn btn-success" @click.native="store" :disabled="form.processing">
                                <feather-icon
                                    icon="SaveIcon"
                                />
                                <span>Guardar</span>
                            </Button>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import { useForm } from '@inertiajs/vue2'
import LayoutContent from '../../Layouts/LayoutContent.vue'
import Card from "../../Components/Card.vue";
import InputError from "../../Components/InputError.vue";
import {alertSuccess, alertWarning} from "../../sweetAlert2";
import Button from "../../Components/Button.vue";
import {currentDate} from "../../helpers";
import Spinner from "../../Components/Spinner.vue";
export default{
    components: {Spinner, Button, InputError, Card, LayoutContent},
    name:'Attendance',
    props:{
        urlRoute:String,
        periodo_clases:Array
    },
    data(){
        return{
            form:useForm({
                id_periodo_clases:0,
                fecha:'',
                detalle:[

                ]
            }),
            todos:''
        }
    },
    mounted(){
        this.reset();
    },
    methods:{
        reset(){
            this.form.reset();
            this.form.fecha=currentDate();
        },
        onTodo(){
            this.form.detalle.map((el)=>{
                el.tipo_asistencia=this.todos
            })
        },
        show(){
            if(this.form.id_periodo_clases==0){
                alertWarning('Selecciones Periodo clases');
                return;
            }
            if(this.form.fecha==''){
                alertWarning('ingrese fecha');
                return;
            }
            this.$refs.btnShow.load();
            this.$http.get(this.routeTo(
                `${this.urlRoute}/lista?id_periodo_clases=${this.form.id_periodo_clases}&fecha=${this.form.fecha}`)
            ).then((res) => {
                this.form.detalle=res.data;
            }).catch((error) => {
                //console.log(error);
            }).finally(()=>{
                this.$refs.btnShow.reset();
            })
        },
        store(){

            if(this.form.detalle.length==0){
                alertWarning('no hay docentes para asistencia');
                return;
            }
            const vacio=this.form.detalle.filter((el)=>{
                return el.tipo_asistencia===''
            }).length;
            if(vacio>0){
                alertWarning(`Hay ${vacio} docente(s) que no esta(n) marcado(s)`);
                return;
            }
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
                this.reset();

            }).catch((error) => {
            }).finally(()=>{
                this.form.processing=false;

            })
        },
    },
    computed:{
        errors(){
            return this.$page.props.errors ||{};
        }
    }
}
</script>
