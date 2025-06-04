<template>
    <div class="form-group z-0">
        <label>Empresa</label>
        <div
            :class="
                errorInput ? 'd-flex border border-danger rounded' : 'd-flex'
            "
        >
            <VSelect
                style="width: 90%"
                :ref="`select_${index}`"
                :path="routeTo(`empresas/autocomplete`)"
                place-holder="Escribe para buscar"
                @input="onSelect"
                :class="{ 'is-invalid': errors }"
                label="empresa"
                :disabled="disabled"
            />
            <slot></slot>
        </div>
        <InputError :error="errorInput ? 'La empresa es obligatoria.' : ''" />
    </div>
</template>
<script>
import VSelect from "../../../Components/VSelect.vue";
import InputError from "../../../Components/InputError.vue";

export default {
    name: "SelectSearch",
    components: {
        InputError,
        VSelect,
    },
    props: {
        errors: String,
        disabled: Boolean,
        index: {
            type: Number,
            default: 0,
        },
        label: {
            type: String,
            default: "",
        },
        errorInput: {
            type: String,
            default: "",
        },
    },
    mounted() {
        //$('.select2-container').css({'width':'100% !important'});
    },
    methods: {
        clean() {
            this.$refs[`select_${this.index}`].clean();
        },
        onSelect(value) {
            this.$emit("input", value, this.index);
        },
        setData(data) {
            this.$refs[`select_${this.index}`].setValue(data);
        },
    },
};
</script>
<style scoped>
.select2-container{
    width: 100% !important;
}
</style>
