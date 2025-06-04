<template>
    <div class="file-input-container">
        <label class="file-input-label">
            <input
                type="file"
                ref="fileInput"
                class="file-input"
                :accept="accept == '*' ? '' : accept"
                @change="handleFileSelect"
                :disabled="disabled"
            />
            <span
                class="file-input-custom"
                :class="{
                    'is-invalid': error,
                    'disabled-state': disabled,
                }"
            >
                <span class="file-input-icon">
                    <feather-icon :icon="uploadIcon" />
                </span>
                <span class="file-input-text">
                    {{ displayText || placeholder }}
                </span>
                <span
                    class="file-input-button btn btn-sm"
                    :class="buttonVariant"
                >
                    {{ buttonText }}
                </span>
            </span>
        </label>
        <small class="form-text text-muted">
            Formatos soportados: {{ accept == "*" ? "Todos" : accept }} (Tamaño
            máximo: 2MB)
        </small>
        <InputError :error="error" />
    </div>
</template>

<script>
import InputError from "./InputError.vue";

export default {
    name: "FileSelector",
    components: {
        InputError,
    },
    props: {
        accept: {
            type: String,
            default: "*",
        },
        placeholder: {
            type: String,
            default: "Seleccionar archivo",
        },
        buttonText: {
            type: String,
            default: "Examinar",
        },
        buttonVariant: {
            type: String,
            default: "btn-primary",
        },
        uploadIcon: {
            type: String,
            default: "UploadIcon",
        },
        error: {
            type: String,
            default: null,
        },
        disabled: {
            type: Boolean,
            default: false,
        },
        modelValue: {
            type: File,
            default: null,
        },
    },
    emits: ["update:modelValue"],
    data() {
        return {
            localFile: null,
        };
    },
    computed: {
        displayText() {
            return this.localFile?.name || "";
        },
    },
    methods: {
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                // 1. Mantener referencia DIRECTA al archivo
                this.localFile = Object.assign(
                    {},
                    {
                        name: file.name,
                        nativeFile: file, // Referencia directa al File original
                    }
                );

                // 2. Emitir el archivo ORIGINAL (no el objeto reactivo)
                this.$emit("update:modelValue", file);
            } else {
                this.localFile = null;
                this.$emit("update:modelValue", null);
            }
            event.target.value = "";
        },
        clearFile() {
            this.$refs.fileInput.value = "";
            this.localFile = null;
            this.$emit("update:modelValue", null);
        },
    },
};
</script>

<style scoped>
.file-input-container {
    position: relative;
    margin-bottom: 1rem;
}

.file-input-label {
    display: block;
}

.file-input {
    position: absolute;
    width: 0.1px;
    height: 0.1px;
    opacity: 0;
    overflow: hidden;
    z-index: -1;
}

.file-input-custom {
    display: flex;
    align-items: center;
    background-color: #f8f9fa;
    border: 1px solid #ced4da;
    border-radius: 4px;
    padding: 8px 12px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-input-custom:hover:not(.disabled-state) {
    background-color: #e9ecef;
    border-color: #adb5bd;
}

.file-input-icon {
    margin-right: 10px;
    color: #495057;
}

.file-input-text {
    flex-grow: 1;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #495057;
}

.file-input-button {
    margin-left: 10px;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.disabled-state {
    background-color: #e9ecef;
    cursor: not-allowed;
    opacity: 0.7;
}

.form-text {
    display: block;
    margin-top: 4px;
    font-size: 0.875em;
    color: #6c757d;
}
</style>
