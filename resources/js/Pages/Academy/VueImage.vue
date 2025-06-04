<script setup>
import {defineProps,computed,onMounted,ref,defineEmits,defineExpose} from 'vue';
import {routeTo} from '../../helpers'
import InputError from "../../Components/InputError.vue";

const emits=defineEmits(['input'])
const props=defineProps({
    default:{
        type:String,
        default:routeTo('images/no_imagen.png')
    },
    label:String,
    error:String
})
const imagePreview=ref(null);
imagePreview.value=props.default;
const idImage=computed(()=>'imagen-'+Math.floor(Math.random() * (999 - 100 + 1)) + 100 );
const idFile=computed(()=>'file-'+Math.floor(Math.random() * (999 - 100 + 1)) + 100 );
const imageClick=()=>{
    document.getElementById(idFile.value).click();
}
const onFileChange=(e)=>{
    const file = e.target.files[0];
    if(file){
         const  reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value=e.target.result
        }
        reader.readAsDataURL(file);
        emits('change',file)
    }
}
const clean=()=>{
    imagePreview.value=props.default;
}
const setValue=(image)=>{
    imagePreview.value=image;
}
onMounted(() => {

})
defineExpose({clean,setValue})
</script>

<template>
    <div class="form-group">
        <label>{{props.label}}</label>
        <img :id="idImage" @click="imageClick" title="click para cambiar imagen"  :src="imagePreview" :alt="props.label" class="img-responsive img-thumbnail">
        <input type="file" @change="onFileChange" style="display: none" :id="idFile"  accept="image/* ">
        <InputError :error="error"/>
    </div>
</template>

<style scoped>
img{
    object-fit: contain;
    cursor: pointer;
    width: 150px;
    height: 150px;
    display: block
}
</style>
