<template>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="form-group">
                <label>Competencia</label>
                <VSelect
                    :ref="`select_${index}`"
                    :path="routeTo(`competencias/${idPlan}/autocomplete`)"

                    place-holder="Escriba para buscar"
                    @input="onSelect"
                    :class="{'is-invalid':errors}"
                    label="competencia"
                    :disabled="disabled"
                />
                <InputError :error="errors"/>
            </div>
        </div>
    </div>

</template>
<script>
import VSelect from '../../../Components/VSelect.vue';
import InputError from '../../../Components/InputError.vue';

export default {
    name:'SelectCompetence',
    components:{
        InputError,
        VSelect
    },
    props:{
        errors:String,
        disabled: Boolean,
        idPlan:Number,
        index:{
            type:Number,
            default:0
        }
    },
    methods:{
        clean(){
            this.$refs[`select_${this.index}`].clean();
        },
        onSelect(value) {
            this.$emit('input', value, this.index);
        },
        setData(data) {
            //console.log('setData',data)
            this.$refs[`select_${this.index}`].setValue(data)
        }
    }
}
</script>
