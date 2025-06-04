<script setup>
import {defineProps,defineEmits,ref,computed,watch} from 'vue';
import InputError from "./InputError.vue";
import {eventBus} from "../helpers";

const emits=defineEmits(['input','update:modelValue'])
const props=defineProps({
    modelValue:[String,Number],
    departamentos:Array,
    error:String,
    index:{
        type:Number,
        default:0
    }
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
    eventBus.$emit(`departamento_${props.index}:change`,newValue)
}, { deep: true })
</script>

<template>
    <div class="form-group">
        <label>Departamento</label>
        <select class="form-control"
            :class="{'is-invalid':error}"
            @input="$emit('update:modelValue', $event.target.value)"
            v-model="value"
        >
            <option value=0>SELECCIONE</option>
            <option v-for="option in departamentos" :key="option.id" :value="option.cod_dpto">{{option.nombre}}</option>
        </select>
        <InputError :error="error"/>
    </div>
</template>

<style scoped>

</style>
