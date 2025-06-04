<template>
    <div class="row">
        <div class="col-md-12 col-lg-12">
            <div class="row">
                <div class="col-md-4 col-lg-4">
                    <div class="form-group">
                        <label>Indicador</label>
                        <select
                            class="form-control"
                            @change="selectCriterios"
                            v-model="id_criterio"
                        >
                            <option
                                :value="index"
                                v-for="(item, index) in indicadores"
                            >
                                {{ item.descripcion }}
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 col-lg-4">
                    <div class="form-group">
                        <label>Formula:</label>
                        <input
                            class="form-control"
                            type="text"
                            value="CT*0.1+CP*0.2+CE*0.4+CTE*0.3"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 40%">
                                    Estudiante
                                </th>
                                <th colspan="4" style="text-align: center">
                                    INDICADOR I
                                </th>
                                <th rowspan="2" style="width: 10%">Promedio</th>
                                <!-- <th colspan="4" style="text-align: center">INDICADOR II</th>
                    <th colspan="4" style="text-align: center">
                        INDICADOR III
                    </th> -->
                            </tr>
                            <tr>
                                <th v-for="(item, index) in criterios">
                                    {{
                                        `${item.descripcion.charAt(0)}${index}`
                                    }}
                                </th>
                                <!-- <th>CT</th>
                        <th>CP</th>
                        <th>CE</th>
                        <th>CTE</th>
                        <th>CT</th>
                        <th>CP</th>
                        <th>CE</th>
                        <th>CTE</th> -->
                            </tr>
                        </thead>
                        <tbody v-for="item in estudiantes">
                            <tr>
                                <td style="border-right: 1px solid #c9c9c9">
                                    <p>{{ item.nombre }}</p>
                                </td>
                                <td v-for="item in criterios">
                                    <input type="number" name="" id="" />
                                </td>
                                <td style="border-right: 1px solid #c9c9c9">
                                    <p></p>
                                </td>
                                <!-- <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td>
                    <td><input type="number" name="" id="" /></td> -->
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "TableCalificarDocente",
    props: {
        indicadores: Array,
    },
    data() {
        return {
            estudiantes: [
                {
                    nombre: "JUAN DAVID RAMIRÉZ MENDOZA",
                },
                {
                    nombre: "SHANDE ANDRES ALVAN RIOS",
                },
                {
                    nombre: "PAOLO SANCHÉZ JULI",
                },
                {
                    nombre: "DANDE DINO UPIACHIWA FINI",
                },
                {
                    nombre: "PABLO GÓNZALES RI",
                },
            ],
            id_criterio: "",
            criterios: [],
        };
    },
    methods: {
        selectCriterios(event) {
            this.criterios =
                this.indicadores[event.target.value].criterios_evaluacion;
            console.log(this.criterios);
        },
    },
};
</script>
<style scoped>
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
        background-color: #f3f2f7;
        border-right: 1px solid #c9c9c9;
    }
    td {
        background-color: white;
        padding: 8px 0 10px 10px;

        input {
            width: 90%;
        }
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
