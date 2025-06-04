<template>
    <div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th
                        v-for="(column, index) in columns"
                        :key="index"
                        class="text-center"
                    >
                        {{ column.title }}
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="data.length === 0">
                    <td :colspan="columns.length">
                        <div
                            class="d-flex flex-column justify-content-center align-items-center mt-1"
                        >
                            <div>
                                <feather-icon
                                    icon="InboxIcon"
                                    class="icon-large"
                                />
                            </div>
                            <p>{{ emptyText }}</p>
                        </div>
                    </td>
                </tr>
                <tr v-else v-for="(row, index) in data" :key="index">
                    <td
                        v-for="(column, colIndex) in columns"
                        :key="colIndex"
                        class="text-center"
                    >
                        <span v-if="column.data === null">{{ index + 1 }}</span>
                        <span v-else>
                            <slot
                                :name="column.data"
                                :row="row"
                                :index="row?.id_horario || index"
                            >
                                {{ row[column.data] }}
                            </slot>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: {
        columns: {
            type: Array,
            required: true,
        },
        data: {
            type: Array,
            required: true,
        },
        emptyText: {
            type: String,
            default: "Sin registros",
        },
    },
};
</script>

<style scoped>
.icon-large {
    width: 48px; /* Ajusta el tamaño según tus necesidades */
    height: 48px; /* Ajusta el tamaño según tus necesidades */
}
</style>
