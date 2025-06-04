<template>
    <LayoutContent>
        <div class="row mb-2">
            <div class="col-md-12 col-lg-12">
                <h4>Plan de Estudio</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12" v-for="item in cursos">
                <Card>
                    <template #header>{{ item.ciclo }}</template>
                    <TablePlanEstudioEstudiantes :cursoCiclo="item.cursos" />
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import LayoutContent from "../../Layouts/LayoutContent.vue";
import TablePlanEstudioEstudiantes from "../Cursos/components/TablePlanEstudioEstudiantes.vue";
import Card from "../../Components/Card.vue";
export default {
    name: "PlanEstudioEstudiantes",
    components: {
        LayoutContent,
        TablePlanEstudioEstudiantes,
        Card,
    },
    props: {
        cursos: Array,
    },
    mounted() {
        console.log(this.cursos);
    },
};
</script>
