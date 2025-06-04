<template>
    <div v-if="visible" class="custom-modal-overlay">
        <div class="custom-modal p-1">
            <div class="custom-feather">
                <feather-icon
                    stroke-width="1px"
                    size="80"
                    color="#fab887"
                    icon="AlertCircleIcon"
                ></feather-icon>
            </div>
            <div class="custom-modal-body">
                <p>{{ message }}</p>
            </div>
            <div class="custom-modal-footer">
                <button class="cancel-button" @click="closeModal">
                    Aceptar
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            required: true,
        },
        message: {
            type: String,
            required: true,
        },
    },
    methods: {
        closeModal() {
            this.$emit("close");
        },
    },
};
</script>

<style scoped>
.custom-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
}

.custom-modal {
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    max-width: 400px;
    width: 100%;
    overflow: hidden;
}

.circle-icon {
    border-radius: 100%;
    border: 1px solid #fab887;
    padding: 16px;
}

.custom-feather {
    display: flex;
    justify-content: center;
    align-items: center;
}

.custom-modal-body {
    padding: 16px;
    text-align: center;
}

.custom-modal-body p {
    margin: 0;
    font-size: 1rem;
    color: #555;
}

.custom-modal-footer {
    padding: 16px;
    padding-top: 0px;
    display: flex;
    justify-content: center;
    gap: 8px;
}

.custom-modal-footer button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

.cancel-button {
    background-color: #6c757d;
    color: white;
}

.cancel-button:hover {
    background-color: #5a6268;
}

.confirm-button {
    background-color: #dc3545;
    color: white;
}

.confirm-button:hover {
    background-color: #c82333;
}
</style>
