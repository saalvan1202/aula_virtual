<template>
    <div class="tooltip-container">
        <div :class="['circle', { inactive: !active }]"></div>
        <span :class="['tooltip', { inatooltip: !active }]">{{
            active ? "Habilitado" : "Deshabilitado"
        }}</span>
    </div>
</template>

<script>
export default {
    name: "OnlineSignal",
    props: {
        active: {
            type: Boolean,
            default: false,
        },
    },
};
</script>

<style scoped>
.tooltip-container {
    position: relative;
    display: inline-flex;
    align-items: center;
}

/* Círculo con efecto neon */
.circle {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 1px solid #00ff4c;
    background-color: #00ff4c;
    box-shadow: 0 0 5px #00ff4c;
    animation: pulse 1.5s infinite alternate ease-in-out;
    cursor: pointer;
}

/* Estado inactivo (rojo) */
.inactive {
    border: 1px solid #ff0000;
    background-color: #ff0000;
    box-shadow: 0 0 5px #ff0000;
    animation: pulse-red 1.5s infinite alternate ease-in-out;
}

/* Tooltip arriba y a la derecha */
.tooltip {
    visibility: hidden;
    background-color: black;
    color: white;
    text-align: center;
    padding: 5px 8px;
    border-radius: 5px;
    font-size: 12px;
    position: absolute;
    bottom: 100%; /* Coloca el tooltip arriba */
    left: 100%; /* Lo posiciona a la derecha del círculo */
    transform: translateX(-24%) translateY(-5px); /* Ajusta la posición */
    white-space: nowrap;
    opacity: 0;
    transition: opacity 0.3s;
}

.inatooltip {
    transform: translateX(-20%) translateY(-5px); /* Ajusta la posición */
}

/* Flecha del tooltip */
.tooltip::after {
    content: "";
    position: absolute;
    top: 100%;
    left: 10%;
    border-width: 5px;
    border-style: solid;
    border-color: black transparent transparent transparent;
}

/* Mostrar el tooltip al pasar el mouse */
.tooltip-container:hover .tooltip {
    visibility: visible;
    opacity: 1;
}

/* Efecto de pulsación verde */
@keyframes pulse {
    0% {
        transform: scale(1);
        box-shadow: 0 0 5px #00ff4c, 0 0 10px #00ff4c;
    }
    50% {
        transform: scale(1.3);
        box-shadow: 0 0 10px #00ff4c, 0 0 20px #00ff4c;
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 5px #00ff4c, 0 0 10px #00ff4c;
    }
}

/* Efecto de pulsación roja */
@keyframes pulse-red {
    0% {
        transform: scale(1);
        box-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000;
    }
    50% {
        transform: scale(1.3);
        box-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000;
    }
    100% {
        transform: scale(1);
        box-shadow: 0 0 5px #ff0000, 0 0 10px #ff0000;
    }
}
</style>
