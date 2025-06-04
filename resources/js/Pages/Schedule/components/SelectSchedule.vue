<template>
    <div class="form-group">
        <label>{{ label }}</label>
        <select
            class="form-control"
            v-model="selectedValue"
            :class="{
                'is-invalid': hasError,
            }"
            @change="handleChange"
            :disabled="disabled"
        >
            <option value="0" disabled>Seleccione una opción</option>
            <template v-if="!customizeOptions && nombre_label">
                <option
                    v-for="option in options"
                    :key="option.id"
                    :value="option.id"
                >
                    {{ option.nombre }}
                </option>
            </template>
            <template v-else-if="!customizeOptions">
                <option
                    v-for="option in options"
                    :key="option.id"
                    :value="option.id"
                >
                    {{ option.descripcion }}
                </option>
            </template>
            <template v-else>
                <slot></slot>
            </template>
        </select>
        <template v-if="!autoValidate">
            <InputError :error="hasError ? errorMessage : ''" />
        </template>
        <template v-else>
            <InputError :error="autoHasError" />
        </template>
    </div>
</template>

<script>
import InputError from "../../../Components/InputError.vue";

export default {
    components: {
        InputError,
    },
    name: "SelectSchedule",
    props: {
        label: {
            type: String,
            default: "",
        },
        options: {
            type: Array,
            required: false,
        },
        modelValue: {
            type: [String, Number, Object],
            default: 0,
        },
        hasError: {
            type: Boolean,
            default: false,
        },
        errorMessage: {
            type: String,
            default: "Campo obligatorio",
        },
        autoValidate: {
            type: Boolean,
            default: false,
        },
        autoHasError: {
            type: String,
            default: "",
        },
        onChange: {
            type: Function,
            default: null, // Prop opcional
        },
        customizeOptions: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        nombre_label: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue"],
    computed: {
        selectedValue: {
            get() {
                return this.modelValue;
            },
            set(value) {
                this.$emit("update:modelValue", value);
            },
        },
    },
    methods: {
        handleChange(event) {
            if (this.onChange) {
                this.onChange(event);
            }
        },
    },
};
</script>
