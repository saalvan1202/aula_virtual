import Vue from 'vue';
import _ from 'lodash';
import {routeTo as baseRouteTo} from './helpers.js';
export default {
    methods:{
        routeTo(url) {
            return baseRouteTo(url)
        },
        asset(url){
            return this.routeTo(url);
        },
        setFormData(form, data){
            Object.keys(form).forEach((key)=>{
                if(data[key]!=undefined){
                    form[key]=data[key];
                }
            })
        },
        isEmpty(value){
            return _.isEmpty(value);
        },
        download(archivo){
            if(this.isEmpty(archivo)){
                return;
            }
            window.open(this.routeTo(`archivos/descargar/${archivo.id}/${archivo.archivo}`),'_blank');

        },
        currentDate() {
            const date = new Date();
            return `${date.getFullYear()}-${(date.getMonth() + 1).toString().padStart(2, 0)}-${date.getDate().toString().padStart(2, 0)}`;
        },
        //FUNCION PARA LIMPIAR ERROR DE VUE HTTP
        clearErrors(errorBag){
            if(errorBag!=undefined){
                Vue.set(this.$page.props.errors,errorBag, {});
                return;
            }
            Vue.set(this.$page.props,'errors',{});
        }

    }
}
