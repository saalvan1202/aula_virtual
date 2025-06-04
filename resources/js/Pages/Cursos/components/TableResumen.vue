<template>
    <div class="table-evaluacion">
        <table>
            <tr>
                <td class="th">Aprobados</td>
                <td>{{ resumen.aprobados }}</td>
            </tr>
            <tr>
                <td class="th">Desaprobados</td>
                <td>{{ resumen.desaprobados }}</td>
            </tr>
            <tr>
                <td class="th">Inhabilitados</td>
                <td>{{ resumen.inhabilitado }}</td>
            </tr>
            <tr>
                <td class="th">Matriculados</td>
                <td>{{ resumen.matriculados }}</td>
            </tr>
        </table>
    </div>
</template>
<script>
import LayoutContent from "../../../Layouts/LayoutContent.vue";
export default {
    name: "TableResumen",
    components: {
        LayoutContent,
    },
    props: {
        resumen: Object,
    },
};
</script>
<style scoped>
.table-evaluacion {
    display: flex;
    flex-direction: column;
    justify-content: start;
    align-items: start;
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

    width: 50%;
    .th {
        height: 6vh;
        padding-left: 10px;
        min-width: 4.5vw;
        border-bottom: 2px solid #fff;
        background-color: #f3f2f7;
    }
    td {
        text-align: center;
        border-bottom: 1px solid #f3f2f7;
        border-top: 2px solid #f3f2f7;
        border-right: 2px solid #f3f2f7;
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
}
</style>
