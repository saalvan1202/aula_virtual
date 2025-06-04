<script setup>
import {ref} from 'vue';
const key_generate=ref('')
const keyRef=ref(null);
const props=defineProps({
    characters:{
        type:Number,
        default:12
    }
})
const generate=()=>{
    const caracteres = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$*_.';
    let contrasena = '';
    for (let i = 0; i < props.characters; i++) {
        const caracterAleatorio = caracteres.charAt(Math.floor(Math.random() * caracteres.length));
        contrasena += caracterAleatorio;
    }
    key_generate.value=contrasena;
}
const copy=()=>{
    console.log('copiando....');
    navigator.clipboard.writeText(key_generate.value)

    var selection = window.getSelection();
    var range = document.createRange();
    range.selectNodeContents(keyRef.value);
    selection.removeAllRanges();
    selection.addRange(range);
}
const clear=()=>{
    key_generate.value='';
}
defineExpose({clear})
</script>

<template>
   <div>
       <button class="btn btn-sm btn-info" @click.prevent="generate">Generar Constraseña</button>
       <span ref="keyRef" class="badge badge-light-dark" style="padding: 0.5rem 0.5rem;font-size: 95%">{{key_generate}}</span>
       <button v-if="key_generate.length>0" type="button" class="btn btn-sm" title="copiar" @click.prevent="copy">
           <feather-icon
               icon="CopyIcon"
           >
           </feather-icon>
       </button>
   </div>
</template>

<style scoped>

</style>
