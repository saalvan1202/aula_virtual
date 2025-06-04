<template>
    <Fragment>
        <div class="row">
            <div class="col-md-6 col-lg-6" >
                <div class="form-group">
                    <label>Tipo Documento</label>
                    <select class="form-control"
                            v-model="form.id_tipo_documento_identidad"
                            :class="{'is-invalid':errors.id_tipo_documento_identidad}"
                    >
                        <option value=0>SELECCIONE</option>
                        <option v-for="option in tipo_documentos" :key="option.id" :value="option.id">{{option.descripcion}}</option>
                    </select>
                    <InputError :error="errors.id_tipo_documento_identidad"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Número Documento</label>
                    <div class="input-group input-group-merge" :class="{'is-invalid':errors.numero_documento}">
                        <input class="form-control" type="text" v-model="form.numero_documento" :class="{'is-invalid':errors.numero_documento}"/>
                        <SearchDni
                            v-if="form.id_tipo_documento_identidad==1"
                            :dni="form.numero_documento"
                            @input="onDni"
                        />
                    </div>
                    <InputError :error="errors.numero_documento"/>

                </div>
            </div>
        </div>
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
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Apellido Paterno</label>
                    <input class="form-control" type="text" v-model="form.apellido_paterno"
                        :class="{'is-invalid':errors.apellido_paterno}"
                        v-input-upper
                    />
                    <InputError :error="errors.apellido_paterno"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Apellido Materno</label>
                    <input class="form-control" type="text" v-model="form.apellido_materno"
                        :class="{'is-invalid':errors.apellido_materno}"
                        v-input-upper
                    />
                    <InputError :error="errors.apellido_materno"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Sexo</label>
                    <select class="form-control"
                            v-model="form.sexo"
                            :class="{'is-invalid':errors.sexo}"
                    >
                        <option value='M'>MASCULINO</option>
                        <option value='F'>FEMENINO</option>
                    </select>
                    <InputError :error="errors.sexo"/>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="form-group">
                    <label>Estado Civil</label>
                    <select class="form-control"
                            v-model="form.estado_civil"
                            :class="{'is-invalid':errors.estado_civil}"
                    >
                        <option value='SOLTERO'>SOLTERO</option>
                        <option value='CASADO'>CASADO</option>
                        <option value='DIVORSIADO'>DIVORSIADO</option>
                        <option value='VIUDO'>VIUDO</option>
                        <option value='CONVIVIENTE'>CONVIVIENTE</option>
                    </select>
                    <InputError :error="errors.estado_civil"/>
                </div>
            </div>
        </div>
    </Fragment>
</template>
<script>
import { Fragment } from 'vue-fragment'
import SearchDni from "./SearchDni.vue";
import InputError from "./InputError.vue";
export default {
    name:'FormPerson',
    components:{InputError, SearchDni, Fragment},
    props:{
        form:Object,
        errors:Object,
    },
    methods:{
        onDni(data){
            this.form.nombres=data.nombres;
            this.form.apellido_paterno=data.apellido_paterno;
            this.form.apellido_materno=data.apellido_materno;
        }
    },
    computed:{
        tipo_documentos(){
            return this.$page.props.tipo_documentos ||{};
        },
    }
}
</script>
