<template>
    <LayoutContent>
        <Card>
            <template #header>
                <h4 class="card-title">
                    Administrar Permisos
                </h4>
            </template>
            <div class="row">
                <div class="col-sm-4">
                    <div class="card card-bordered">
                        <div class="card-header" style="padding: 10px 15px;border-bottom: 1px solid transparent;border-color: #ddd;">
                            <h4 class="card-title">Opciones</h4>
                        </div>
                        <div class="card-content pt-1">
                            <div class="card-body" style="height: 320px;overflow-y: auto">
                                <div class="row">
                                    <div class="col-md-12 col-lg-12">
                                        <div class="input-group form-group">
                                            <div class="input-group-prepend">
                                                    <span class="input-group-text">Perfil</span>
                                            </div>
                                            <select class="form-control"
                                                v-model="form.id_perfil"
                                                @change="onMenu"
                                            >
                                                <option value="0">SELECCIONE</option>
                                                <option v-for="option in perfiles" :key="option.id" :value="option.id">{{option.descripcion}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button class="btn btn-success" @click.prevent="store" :disabled="loading">
                                            <feather-icon
                                                icon="SaveIcon"
                                            />
                                            <span>Guardar</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-8">
                    <div class="card card-bordered">
                    <div class="card-header" style="padding: 10px 15px;border-bottom: 1px solid transparent;border-color: #ddd;">
                        <h4 class="card-title">
                            Modulos
                        </h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body" style="height: 320px;overflow-y: auto">
                            <div id="tree" class="jstree jstree-2 jstree-default" :class="{'sk-loading':loading}">
                                <Spinner v-if="loading"/>
                                <ul class="jstree-container-ul jstree-children">
                                    <li class="jstree-node jstree-closed"
                                        v-for="(menu,index) in menus" :key="menu.id"
                                        :class="[index==menus.length-1?'jstree-last':'']"
                                    >
                                        <i class="jstree-icon jstree-ocl" role="presentation" @click="collapse($event)"></i>
                                        <a class="jstree-anchor" :class="[menu.estado=='A'?'jstree-checked':'']" :for="'jstree_'+menu.id" @click="onClick(menu)">
                                            <i class="jstree-icon jstree-checkbox" :class="undetermined[index]" role="presentation"></i>
                                            {{menu.titulo}}
                                        </a>
                                        <ul class="jstree-children">
                                            <li class="jstree-node  jstree-leaf" v-for="(item,idx) in menu.children" :key="item.id"
                                                :class="[idx==menu.children.length-1?'jstree-last':'']"
                                            >
                                                <i class="jstree-icon jstree-ocl" role="presentation"></i>
                                                <a class="jstree-anchor " :class="[item.estado=='A'?'jstree-checked':'']" @click="onClick(item)">
                                                    <i class="jstree-icon jstree-checkbox"  role="presentation"></i>
                                                    {{item.titulo}}
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </Card>
    </LayoutContent>
</template>
<script>
import { useForm } from '@inertiajs/vue2'
import Vue from 'vue';
import LayoutContent from '../../Layouts/LayoutContent.vue'
import Card from '../../Components/Card.vue'
import { alertSuccess,alertWarning } from '../../sweetAlert2'
import Spinner from '../../Components/Spinner.vue';
export default {
    components: { LayoutContent, Card, Spinner },
    name:'Permission',
    props:{
        urlRoute:String,
        perfiles:Array,
    },
    data(){
        return{
            form:useForm({
                id_perfil:0,
                modulos:[]
            }),
            menus:[],
            loading:false,
        }
    },
    methods:{
        onMenu(){
            this.loading=true;
            this.$http.get(this.routeTo(`${this.urlRoute}/${this.form.id_perfil}/edit`)).then((res) => {

                Vue.set(this.$data,'menus',res.data);
            }).catch((error) => {

            }).finally(()=>{
                this.loading=false;
            })
        },
        collapse(e) {
            e.target.parentElement.classList.toggle("jstree-open");
            e.target.parentElement.classList.toggle("jstree-closed");
            e.target.classList.toggle("jstree-icon-down");
        },
        onClick(menu){
            let estado='A';
            if(menu.estado=='A')
                estado='I';
            menu.estado=estado;
            this.onChangeChildren(menu);
        },
        onChangeChildren(menu){
            const estado=menu.estado;
            if(menu.children==null|| !menu.children.length){
                return;
            }
            menu.children.map((item)=>{
                item.estado=estado;
            })
        },
        store(){
            if (!this.menus.length) {
                alertWarning('no existe menu para guardar');
                return;
            }
            this.form.modulos = [];

            const children=(children)=>{
                children.forEach((item)=>{
                    this.form.modulos.push({
                        id: item.id,
                        estado: item.estado
                    })
                    if(item.children.length){
                        children(item.children);
                    }
                })

            }

            this.menus.forEach((item)=>{
                this.form.modulos.push({
                    id: item.id,
                    estado: item.estado
                })
                if(item.children.length){
                    children(item.children);
                }

            });

            this.loading=true;
            this.$http({
                method:'POST',
                url: this.routeTo(`${this.urlRoute}`),
                data: this.form.data()
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');
                this.form.modulos = [];
            }).catch((error) => {
            }).finally(()=>{
                this.loading=false;
            })
        }
    },
    computed:{
        undetermined(index){
            return this.menus.map((menu)=>{
                let className='';
                let totalchildren=menu.children.length;
                let checkedchildren=menu.children.filter((item)=>item.estado=='A').length;
                if(totalchildren==0 && menu.estado=='A'){
                    className='jstree-checkbox';
                }
                if(totalchildren==checkedchildren && totalchildren>0){
                    className='jstree-checkbox';
                }else if(menu.estado=='A' && totalchildren>0){
                    className='jstree-undetermined';
                }
                if(className=='jstree-checkbox'){
                    menu.estado='A';
                }
                return className;
            });
        }
    }
}
</script>
