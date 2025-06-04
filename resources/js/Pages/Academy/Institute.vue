<template>
    <LayoutContent>
        <section>
            <div class="row">
                <div class="col-md-12 col-lg-12">
                   <!-- <button  class="btn btn-primary" @click.prevent="create">
                        <feather-icon
                        icon="PlusIcon"
                        class="mr-50"
                    />
                    <span class="align-middle">Nuevo</span>
                    </button>-->
                    <Button ref="btnEdit"  class="btn btn-warning" @click.native.prevent="edit">
                        <feather-icon
                        icon="EditIcon"
                        class="mr-50"
                        />
                        <span class="align-middle">Modificar</span>
                    </Button>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-12">
                    <Card>
                        <template #header>
                            <h4 class="card-title">
                                Administrar Instituto
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
            <Modal ref="modalProfile" :loading="form.processing">
                <template #title>Instituto</template>
                <form class="form form-vertical">
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Nombre Institucion</label>
                                <input class="form-control" type="text" v-model="form.denominacion"
                                    :class="{'is-invalid':errors.denominacion}"
                                    v-input-upper
                                />
                                <InputError :error="errors.denominacion"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Codigo</label>
                                <input class="form-control" type="text" v-model="form.codigo"
                                       :class="{'is-invalid':errors.codigo}"
                                       v-input-upper
                                />
                                <InputError :error="errors.codigo"/>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Tipo Gestion</label>
                                <select class="form-control"
                                        v-model="form.id_tipo_gestion_educativa"
                                        :class="{'is-invalid':errors.id_tipo_gestion_educativa}"
                                >
                                    <option value=0>SELECCIONE</option>
                                    <option v-for="option in tipo_gestion_educativas" :key="option.id" :value="option.id">{{option.descripcion}}</option>
                                </select>
                                <InputError :error="errors.id_tipo_gestion_educativa"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectDepartament
                                :departamentos="departamentos"
                                :error="errors.id_ubigeo"
                                :modelValue="cod_dpto"
                                @update:modelValue="$event => (cod_dpto = $event)"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectProvince
                                :error="errors.id_ubigeo"
                                :modelValue="cod_prov"
                                @update:modelValue="$event => (cod_prov = $event)"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <SelectDistrict
                                :error="errors.id_ubigeo"
                                :modelValue="form.id_ubigeo"
                                @update:modelValue="$event => (form.id_ubigeo = $event)"
                            />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 col-lg-12">
                            <div class="form-group">
                                <label>Direccion</label>
                                <input class="form-control" type="text" v-model="form.direccion"
                                       :class="{'is-invalid':errors.direccion}"
                                       v-input-upper
                                />
                                <InputError :error="errors.direccion"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <div class="form-group">
                                <label>Correo</label>
                                <input class="form-control" type="email" v-model="form.email"
                                       :class="{'is-invalid':errors.email}">
                                <InputError :error="errors.email"/>
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
                    <div class="row">
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
                    <div class="row">
                        <div class="col-md-6 col-lg-6">
                            <VueImage ref="logo" @change="onLogo" label="Logo" :error="errors.logo"/>
                        </div>
                        <div class="col-md-6 col-lg-6">
                            <VueImage ref="banner" @change="onBanner" label="Banner" :error="errors.banner"/>
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
import {firstId} from "../../helpers";
import VueSelect from "../../Components/VSelect.vue";
import VueImage from "./VueImage.vue";
import {objectToFormData} from "../../formData";
import SelectDepartament from "../../Components/SelectDepartament.vue";
import SelectProvince from "../../Components/SelectProvince.vue";
import SelectDistrict from "../../Components/SelectDistrict.vue";
export default{
    components: {
        SelectDistrict,
        SelectProvince,
        SelectDepartament, VueImage, VueSelect, LayoutContent, Card, DataTable, Modal,InputError,Button },
    name:'Institute',
    props:{
        urlRoute:String,
        tipo_gestion_educativas:Array,
        departamentos:Array
    },
    data(){
        return{
            columns:[
                { data: null, title: '#' ,orderable:false,searchable:false,render:function(data, type, full, meta){
                        return meta.row + 1 + meta.settings._iDisplayStart;
                    }
                },
                { data: 'denominacion', title: 'Denominacion' },
                { data: 'codigo', title: 'Codigo' },
                { data: 'direccion', title: 'Direccion' },
                { data: 'telefono', title: 'Telefono' },
            ],
            form:useForm({
                id:'-1',
                denominacion:'',
                codigo:'',
                direccion:'',
                telefono:'',
                id_tipo_gestion_educativa:0,
                director_general:'',
                email:'',
                logo:null,
                banner:null,
                id_ubigeo:0,
            }),
            cod_dpto:'0',
            cod_prov:'0'
        }
    },
    methods:{
        onLogo(file){
          this.form.logo=file;
        },
        onBanner(file){
            this.form.banner=file;
        },
        reset(){
            this.form.reset();
            this.$refs.logo.clean();
            this.$refs.banner.clean();
            this.cod_dpto='0';
            this.cod_prov='0';
            this.clearErrors();
        },
        setForm(data){
            this.setFormData(this.form,data);
            if(data.logo){
                this.$refs.logo.setValue(data.logo);
            }
            if(data.banner){
                this.$refs.banner.setValue(data.banner);
            }
            if(data.ubigeo){
                this.cod_dpto=data.ubigeo.cod_dpto
            }
            if(data.ubigeo){
                this.cod_prov=data.ubigeo.cod_prov
            }
        },
        create(){
            this.reset();
            this.form.id_tipo_gestion_educativa=firstId(this.tipo_gestion_educativas)
            this.$refs.modalProfile.show();
        },
        close(){
            this.$refs.modalProfile.hide();
        },
        store(){
            this.form.processing=true;
            this.$http({
                method:'POST',
                url: this.routeTo(this.urlRoute),
                data: objectToFormData(this.form.data()),
            }).then((res) => {
                alertSuccess('Datos Guardados Correctamente');
                this.$refs.datatable.reload();
                this.$refs.modalProfile.hide();

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
                this.$refs.modalProfile.show();
            }).catch((error) => {
                //console.log(error);
            }).finally(()=>{
                this.$refs.btnEdit.reset();
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
