<template>
    <button :disabled="isLoading">
        <span v-if="isLoading" class="spinner-border spinner-border-sm"></span>
        <slot/>
    </button>
</template>
<script>

export default{
    name:'Button',
    props:{
        loading:{
            type:Boolean,
            default:false
        }
    },
    data: (p) => ({
        isLoading:p.loading
    }),
    methods:{
        load(){
            this.isLoading=true;
        },
        reset(){
            this.isLoading=false;
        }
    },
    watch: {
        loading: {
            immediate: true,
            handler(value){
                this.isLoading=value;
            }
        }
    }

}

</script>
