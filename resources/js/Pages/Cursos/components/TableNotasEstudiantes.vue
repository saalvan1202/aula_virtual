<template>
    <div class="table-responsive">
        <table class="table">
            <tr>
                <th>Nombre</th>
                <th>Porcentaje</th>
                <th>Nota</th>
            </tr>
            <tbody v-if="notas.length == 0">
                <tr>
                    <td colspan="3">
                        <section class="not-length" style="border-bottom: 0">
                            <feather-icon icon="InboxIcon" />
                            <span class="align-small"
                                >No se registraron notas</span
                            >
                        </section>
                    </td>
                </tr>
            </tbody>
            <tbody v-for="item in notas" :key="item.id">
                <tr class="indicador">
                    <!-- <td colspan="4">Criterios de Evalución</td> -->
                    <td>{{ item.descripcion }}</td>
                    <td>{{ parseFloat(item.porcentaje) }}%</td>
                    <td v-if="item.nota < 12.5" style="color: red">
                        {{ item.nota == 0 ? "-" : item.nota }}
                    </td>
                    <td v-if="item.nota >= 12.5" style="color: black">
                        {{ item.nota == 0 ? "-" : item.nota }}
                    </td>
                </tr>
                <tr v-for="criterio in item.criterios" :key="criterio.id">
                    <td>{{ criterio.nombre }}</td>
                    <td>{{ parseFloat(criterio.porcentaje) }}%</td>
                    <td v-if="criterio.nota < 12.5" style="color: red">
                        {{ criterio.nota == 0 ? "-" : criterio.nota }}
                    </td>
                    <td v-if="criterio.nota >= 12.5">
                        {{ criterio.nota == 0 ? "-" : criterio.nota }}
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr style="border-bottom: 1px solid #c9c9c9">
                    <th colspan="2">Nota Final</th>
                    <th v-if="notaFinal">{{ parseFloat(notaFinal) }}</th>
                    <th v-else>-</th>
                </tr>
            </tfoot>
        </table>
    </div>
</template>
<script>
export default {
    name: "TableNotasEstudiantes",
    props: {
        evaluaciones: Array,
        notas: Array,
        notaFinal: String,
    },
    data() {
        //FUNDAMENTOS DE LAS PROGRAMAICÓN EN BASE DE LAS METODOLOGÍAS ÁGILES
        return {};
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
@keyframes scaleIn {
    0% {
        opacity: 0; /* Comienza invisible */
        height: 10%;
    }
    30% {
        height: 30%;
    }
    60% {
        height: 60%;
    }
    70% {
        height: 70%;
    }
    90% {
        height: 90%;
    }
    100% {
        opacity: 1;
        height: 100%;
    }
}
.table-evaluacion {
    display: flex;
    justify-content: center;
    align-items: center;
    animation: scaleIn 1s ease forwards;
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
        border-bottom: 1px solid #c9c9c9;
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
