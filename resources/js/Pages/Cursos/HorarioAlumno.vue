<template>
    <LayoutContent class="nav-var">
        <Card>
            <template #header>
                <div
                    id="title"
                    style="
                        width: 100%;
                        display: flex;
                        justify-content: space-between;
                    "
                >
                    <h4>HORARIO - {{ periodo }}</h4>
                    <button class="btn btn-outline-dark" @click="imprimir">
                        <feather-icon icon="PrinterIcon" />
                        <span>Imprimir</span>
                    </button>
                </div>
            </template>
            <div class="calendar-table" id="calendar">
                <div class="horario-column">
                    <div class="hora-label">HORA</div>
                    <div v-for="hora in horas" :key="hora" class="hora-item">
                        {{ hora }}
                    </div>
                </div>
                <div class="calendar-container">
                    <div class="calendar-day" v-for="dia in dias" :key="dia">
                        <div class="day-label">{{ dia }}</div>
                        <div
                            style="height: 60px; border-bottom: 1px dotted #ddd"
                            v-for="hora in horas"
                        ></div>
                        <div class="conteniter">
                            <div
                                :class="
                                    hoy == dia
                                        ? 'events-containerD'
                                        : 'events-container'
                                "
                            >
                                <hr
                                    :style="{
                                        position: 'relative',
                                        border: '0.5px solid rgba(255, 0, 0, 0.3)',
                                        top: calcularPosicion(hora),
                                        zIndex: 2,
                                    }"
                                />
                                <div
                                    v-for="(evento, index) in getHorario[dia]"
                                    :key="index"
                                    class="event"
                                    :style="{
                                        top: calcularPosicion(
                                            evento.hora_inicio
                                        ),
                                        height: calcularAltura(
                                            evento.hora_inicio,
                                            evento.hora_fin
                                        ),
                                        fontSize: '10px',
                                        color: '#000',
                                        backgroundColor: 'white',
                                        border: `1px solid ${
                                            colores[index % colores.length]
                                        }`,
                                    }"
                                >
                                    <div
                                        style="
                                            display: flex;
                                            align-items: center;
                                            gap: 5px;
                                            font-weight: bold;
                                        "
                                    >
                                        <div
                                            :style="{
                                                height: '5px',
                                                width: '4px',
                                                borderRadius: '60%',
                                                backgroundColor:
                                                    colores[
                                                        index % colores.length
                                                    ],
                                            }"
                                        ></div>
                                        {{ evento.hora_inicio }} -
                                        {{ evento.hora_fin }}
                                    </div>
                                    <div>
                                        {{ evento.descripcion }}
                                    </div>
                                    <p class="event-details">
                                        ({{ evento.tipo }} - {{ evento.aula }})
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Card>
    </LayoutContent>
</template>

<script>
import LayoutContent from "../../Layouts/LayoutContent.vue";
import Card from "../../Components/Card.vue";
export default {
    name: "HorarioCompacto",
    props: {
        hoy: String,
        horarios: Array,
        hora: String,
        periodo: String,
    },
    data() {
        return {
            dias: [
                "LUNES",
                "MARTES",
                "MIÉRCOLES",
                "JUEVES",
                "VIERNES",
                "SABADO",
                "DOMINGO",
            ],
            colores: [
                "blue",
                "teal",
                "green",
                "orange",
                "purple",
                "yellow",
                "cyan",
                "magenta",
                "lime",
                "black",
            ],
            horas: [
                "06:00",
                "07:00",
                "08:00",
                "09:00",
                "10:00",
                "11:00",
                "12:00",
                "13:00",
                "14:00",
                "15:00",
                "16:00",
                "17:00",
                "18:00",
                "19:00",
                "20:00",
                "21:00",
                "22:00",
                "23:00",
                "24:00",
            ],
            getHorario: {
                LUNES: [],
                MARTES: [],
                MIÉRCOLES: [],
                JUEVES: [],
                VIERNES: [],
                SABADO: [],
                DOMINGO: [],
            },
        };
    },
    mounted() {
        console.log(this.hora);
        this.organizarHorario();
    },
    methods: {
        imprimir() {
            window.print();
        },
        organizarHorario() {
            this.horarios.forEach((horario) => {
                this.getHorario[horario.dia].push({ ...horario });
            });
        },
        calcularPosicion(hora) {
            const [horas, minutos] = hora.split(":").map(Number);
            return `${(horas - 6) * 60 + minutos}px`;
        },
        calcularAltura(horaInicio, horaFin) {
            const [horaI, minutoI] = horaInicio.split(":").map(Number);
            const [horaF, minutoF] = horaFin.split(":").map(Number);
            return `${(horaF - horaI) * 60 + (minutoF - minutoI)}px`;
        },
    },
    components: {
        LayoutContent,
        Card,
    },
};
</script>

<style scoped>
@media print {
    .event {
        margin-top: 15px;
    }
    .events-containerD {
        background-color: transparent !important;
    }
    hr {
        display: none;
    }
    /* Ocultar TODO por defecto */
    body * {
        visibility: hidden !important;
    }
    #title,
    #title h4 {
        color: black;
        font-weight: bold !important;
        justify-content: center !important;
        visibility: visible !important;
        padding-left: 30px !important;
    }
    /* Mostrar solo el div con ID "calendar" */
    #calendar,
    #calendar * {
        visibility: visible !important;
    }
}
.conteniter {
    position: absolute;
    overflow: auto;
    top: 13px;
    height: 100%;
    width: 100%;
    border-radius: 4px;
    color: white;
}
.calendar-table {
    display: flex;
    overflow-x: auto;
    overflow-y: hidden;
}
.horario-column {
    width: 60px;
}
.hora-item {
    height: 60px;
    text-align: center;
    padding-right: 5px;
    line-height: 60px;
    font-size: 10px;
    border-bottom: 1px solid #ddd;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
}
.calendar-container {
    flex: 1;
    display: flex;
    gap: 0;
}
.calendar-day {
    flex: 1;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    border-right: 1px solid #ddd;
    padding-top: 7px;
    position: relative;
}
.hora-label {
    font-weight: bold;
    text-align: center;
    border-right: 1px solid #ddd;
    padding-top: 6px;
    height: 29px;
    border-bottom: 1px solid #ddd;
    border-top: 1px solid #ddd;
    border-left: 1px solid #ddd;
}
.day-label {
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #ddd;
}
.events-container {
    position: relative;
    height: 97.7%;
}
.events-containerD {
    position: relative;
    height: 97.7%;
    background-color: rgba(40, 199, 111, 0.12);
}
.event {
    position: absolute;
    overflow: auto;
    left: 5px;
    width: calc(100% - 10px);
    padding: 5px;
    border-radius: 4px;
    color: white;
}
.event-details {
    font-size: 0.8em;
    margin: 0 !important;
    margin-top: 2px;
}
</style>
