<template>
    <div v-if="loading" class="progress-container">
        <b-progress
            :max="100"
            :value="progress"
            animated
            variant="success"
        ></b-progress>
    </div>
</template>

<script>
export default {
    name: "ProgressTime",
    data() {
        return {
            loading: false,
            progress: 0,
        };
    },
    methods: {
        startLoading() {
            this.loading = true;
            this.progress = 0;
            // Simulación de progreso
            let progressInterval = setInterval(() => {
                if (this.progress < 100) {
                    this.progress += 10; // Aumenta tu barra aquí
                } else {
                    clearInterval(progressInterval);
                    this.loading = false;
                }
            }, 1000); // Ajusta el intervalo a tu gusto
        },
    },
};
</script>

<style scoped>
.progress-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 9999;
}

.progress-container .b-progress {
    height: 2px; /* Puedes ajustar la altura de la barra aquí */
    width: 100%;
}
</style>
