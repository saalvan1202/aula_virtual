<template>
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th style="min-width: 600px">Curso</th>
                <th title="Creditos Teoría">CT</th>
                <th title="Creditos Práctica">CP</th>
                <th title="Horas Teoría">HT</th>
                <th title="Horas Práctica">HP</th>
            </tr>
            <tbody>
                <tr v-for="item in cursoCiclo">
                    <td>{{ item.descripcion }}</td>
                    <td>{{ item.creditos_teoria }}</td>
                    <td>{{ item.creditos_practica }}</td>
                    <td>{{ item.horas_teoria }}</td>
                    <td>{{ item.horas_practica }}</td>
                </tr>
            </tbody>
        </table>
        <div class="not-length" v-if="cursoCiclo.length == 0">
            <feather-icon icon="InboxIcon" />
            <span class="align-small"
                >Aún no se registraron cursos en este ciclo</span
            >
        </div>
    </div>
</template>
<script>
export default {
    name: "TablePlanEstudioEstudiantes",
    props: {
        cursoCiclo: Array,
        evaluaciones: Array,
    },
    data() {
        return {
            notas: [
                {
                    unidad: "UNIDAD I",
                    porcentaje: 30,
                    nota: "",
                    criterios: [
                        {
                            nombre: "Paricipaciones",
                            porcentaje: 10,
                            nota: 10,
                            id: 1,
                        },
                        {
                            nombre: "Laboratorio",
                            porcentaje: 25,
                            nota: 15,
                            id: 2,
                        },
                        {
                            nombre: "Examen Parcial I",
                            porcentaje: 30,
                            nota: 12,
                            id: 4,
                        },
                        {
                            nombre: "Producto Académico",
                            porcentaje: 35,
                            nota: 11,
                            id: 3,
                        },
                    ],
                },
            ],
        };
    },
    mounted() {
        console.log(this.cursoCiclo.cursos);
    },
};
</script>
<style scoped>
.not-length {
    height: 50px;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}
.table-evaluacion {
    display: flex;
    justify-content: center;
    align-items: center;
}
table {
    .indicador {
        background-color: #c9c9c9;
        width: 100%;
        font-weight: bold;
    }
    .action {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 6vh;
        padding-left: 0px;
    }

    width: 100%;
    th {
        height: 6vh;
        padding-left: 10px;
        min-width: 8vw;
        background-color: #f3f2f7;
    }
    td {
        padding: 8px 0 10px 10px;
    }
    .inputD {
        height: 6vh;
        width: 10vh;
        text-align: center;
    }
    .inputE {
        height: 6vh;
        width: 10vh;
        text-align: center;
        outline: none;
    }
    .btn {
    }
}
</style>
