<script setup>
import {defineProps,defineEmits,onMounted,ref,defineExpose,watch,computed} from 'vue';
import InputError from "./InputError.vue";
import {eventBus,routeTo,getVueInstance} from "../helpers";

const currentInstance=getVueInstance();
const emits=defineEmits(['input','update:modelValue'])
const props=defineProps({
    modelValue:[String,Number],
    error:String,
    index:{
        type:Number,
        default:0
    }
})
const provincias=ref([]);
const cod_dpto=ref(null);

onMounted(() => {
    eventBus.$on(`departamento_${props.index}:change`,(e)=>{
        getProvinces(e);
    })
})
const getProvinces=(coddpto)=>{
    cod_dpto.value=coddpto
    if(coddpto=='0'){
        provincias.value=[];
        value.value='0'
        return;
    }
    currentInstance.$http.get(routeTo(`ubigeos/provincias?cod_dpto=${coddpto}`)).then((res) => {
        provincias.value=res.data;
    }).catch((error) => {
    }).finally(()=>{
    })
}
const value = computed({
    get() {
        return props.modelValue
    },
    set(newValue) {
        emits('update:modelValue', newValue)
    }
})
watch(value, newValue => {
    eventBus.$emit(`provincia_${props.index}:change`,cod_dpto.value,newValue)
})

defineExpose(['getProvinces']);
</script>

<template>
    <div class="form-group">
        <label>Provincia</label>
        <select class="form-control"
            :class="{'is-invalid':error}"
            @input="$emit('update:modelValue', $event.target.value)"
            v-model="value"
        >
            <option value=0>SELECCIONE</option>
            <option v-for="option in provincias" :key="option.id" :value="option.cod_prov">{{option.nombre}}</option>
        </select>
        <InputError :error="error"/>
    </div>
</template>

<style scoped>

</style>
