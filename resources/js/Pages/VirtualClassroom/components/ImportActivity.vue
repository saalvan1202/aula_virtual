<template>
    <div class="add-activity" @click="handleClick">
        <div class="add-activity__plus-icon">
            <span>
                <feather-icon
                    size="20"
                    stroke-width="3px"
                    icon="DownloadIcon"
                ></feather-icon>
            </span>
        </div>
        <p class="p-0 m-0">{{ title }}</p>
    </div>
</template>

<script>
export default {
    name: "ImportActivity",
    props: {
        title: {
            type: String,
            required: true,
        },
    },
    methods: {
        handleClick() {
            this.$emit("add");
            this.$emit("id_seccion");
        },
    },
};
</script>

<style scoped>
.add-activity {
    display: flex;
    justify-content: start;
    align-items: center;
    border-radius: 8px;
    gap: 20px;

    background-color: #f5f9fc;
    color: #28c76f;
    border: 1px solid #28c76f;
    padding: 15px 10px;
}

.add-activity:hover {
    background-color: #cfe2f1;
    cursor: pointer;
    span,
    p {
        color: #179b52;
    }

    p {
        text-decoration: underline;
    }
}

.add-activity__plus-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #28c76f;
    padding: 5px;

    span {
        color: #28c76f;
    }
}
</style>
