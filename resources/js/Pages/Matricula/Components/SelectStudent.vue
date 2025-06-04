<template>
    <div class="form-group">
        <label
            >Estudiante
            <span style="color: red; margin-left: 2px"> * </span>
        </label>
        <VSelect
            :ref="`select_${index}`"
            :path="routeTo(`matriculas/autocomplete`)"
            place-holder="Escribe para buscar"
            @input="onSelect"
            :class="{ 'is-invalid': errors }"
            label="estudiante"
            :disabled="disabled"
        />
        <InputError :error="errors" />
    </div>
</template>
<script>
import VSelect from "../../../Components/VSelect.vue";
import InputError from "../../../Components/InputError.vue";

export default {
    name: "SelectStudent",
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
    },
    methods: {
        clean() {
            this.$refs[`select_${this.index}`].clean();
        },
        onSelect(value) {
            this.$emit("input", value, this.index);
        },
        setData(data) {
            //console.log('setData',data)
            this.$refs[`select_${this.index}`].setValue(data);
        },
    },
};
</script>
