<template>
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nombres</th>
                    <th>Documento</th>
                    <th>Numero</th>
                    <th>Parentesco</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(item,index) in items" :key="index">
                    <td>{{item.apoderado}}</td>
                    <td>{{item.tipo_documento}}</td>
                    <td>{{item.numero_documento}}</td>
                    <td>
                        <select class="form-control" v-model="item.parentesco">
                            <option value="MADRE">MADRE</option>
                            <option value="PADRE">PADRE</option>
                            <option value="HERMANO">HERMANO</option>
                            <option value="HERMANO">HERMANO</option>
                            <option value="TIO">TIO</option>
                            <option value="TIA">TIA</option>
                        </select>
                    </td>
                    <td>
                        <a @click.prevent="remove(index)" href="" class="text-danger"><feather-icon icon="Trash2Icon" class="mr-50" title="Registrar Apoderado" /></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
import VSelect from "../../Components/VSelect.vue";
import SelectStudent from "./Components/SelectStudent.vue";

export default{
    name:'Apoderado',
    components: {SelectStudent, VSelect},
    props:{
        items:Array
    },
    methods:{
        remove(index){
            this.items.splice(index, 1)
        }
    }

}
</script>

<style scoped>

</style>
