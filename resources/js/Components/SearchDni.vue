<template>
    <BInputGroupAppend is-text
                       class="cursor-pointer"
                       @click="searchDni"
    >
        <feather-icon
            icon="SearchIcon"
            v-show="!loading"
        />
        <BSpinner small v-show="loading"/>
    </BInputGroupAppend>
</template>
<script>
import {BInputGroupAppend,BSpinner} from 'bootstrap-vue';
import { alertWarning } from '../sweetAlert2.js';
export default {
    name:'SearchDni',
    components:{BInputGroupAppend,BSpinner},
    props:{
        dni:String
    },
    data(){
        return{
            loading:false
        }
    },
    methods:{
        searchDni(){
            this.loading=true;
            this.$http.get(this.routeTo(`usuarios/buscar/dni/${this.dni}`)).then((res) => {
                this.$emit('input',res.data);
            }).catch((error) => {
                if (error.response && error.response.status == 404){
                    alertWarning(error.response.data);
                }
            }).finally(()=>{
                this.loading=false
            })
        },
    }
}
</script>
