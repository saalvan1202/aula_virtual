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
const distritos=ref([]);
const cod_dpto=ref(null);
const cod_prov=ref(null);
onMounted(() => {
    eventBus.$on(`provincia_${props.index}:change`,(coddpto,codprov)=>{
        load(coddpto,codprov);
    })
})
const value = computed({
    get() {
        return props.modelValue
    },
    set(newValue) {
        emits('update:modelValue', newValue)
    }
})

watch(value, newValue => {
    eventBus.$emit(`distrito_${props.index}:change`,cod_dpto.value,cod_prov.value,newValue)
},{ deep: true })

const load=(coddpto,codprov)=>{
    cod_dpto.value=coddpto;
    cod_prov.value=codprov;
    if(coddpto=='0' || codprov=='0'){
        distritos.value=[];
        value.value='0';
        return;
    }
    currentInstance.$http.get(routeTo(`ubigeos/distritos?cod_dpto=${coddpto}&cod_prov=${codprov}`)).then((res) => {
        distritos.value=res.data;
    }).catch((error) => {
        //console.log(error);
    }).finally(()=>{
    })
}
defineExpose(['load']);
</script>

<template>
    <div class="form-group">
        <label>Distrito</label>
        <select class="form-control"
            :class="{'is-invalid':error}"
            @input="$emit('update:modelValue', $event.target.value)"
            v-model="value"
        >
            <option value=0>SELECCIONE</option>
            <option v-for="option in distritos" :key="option.id" :value="option.id">{{option.nombre}}</option>
        </select>
        <InputError :error="error"/>
    </div>
</template>

<style scoped>

</style>
