<template>
    <div class="form-group">
        <label
            >{{ label }}
            <span style="color: red" v-if="requerido">*</span></label
        >
        <div class="input-group">
            <input
                :type="type"
                class="form-control"
                v-model="inputValue"
                :class="{
                    'is-invalid': hasError,
                }"
                :min="min"
                :max="max"
                @change="handleChange"
                @click="handleClick"
                :readonly="readOnly"
                :disabled="disabled"
            />
            <template v-show="withButton">
                <div class="input-group-append">
                    <slot></slot>
                </div>
            </template>
        </div>
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
        requerido: Boolean,
        label: {
            type: String,
            default: "",
        },
        type: {
            type: String,
            default: "text",
        },
        min: {
            type: String,
            required: false,
        },
        max: {
            type: String,
            required: false,
        },
        autoValidate: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: [String, Number],
            default: 0,
        },
        hasError: {
            type: Boolean,
            default: false,
        },
        autoHasError: {
            type: String,
            default: "",
        },
        errorMessage: {
            type: String,
            default: "El periodo de clases es obligatorio.",
        },
        onChange: {
            type: Function,
            default: null,
        },
        readOnly: {
            type: Boolean,
            default: false,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        withButton: {
            type: Boolean,
            default: false,
        },
    },
    emits: ["update:modelValue"],
    computed: {
        inputValue: {
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
        handleClick() {
            this.$emit("click-input");
        },
    },
};
</script>
