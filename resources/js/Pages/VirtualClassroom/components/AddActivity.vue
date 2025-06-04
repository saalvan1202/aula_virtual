<template>
    <div class="add-activity" @click="handleClick">
        <div class="add-activity__plus-icon">
            <span>
                <feather-icon
                    size="20"
                    stroke-width="3px"
                    icon="PlusIcon"
                ></feather-icon>
            </span>
        </div>
        <p class="p-0 m-0">{{ title }}</p>
    </div>
</template>

<script>
export default {
    props: {
        title: {
            type: String,
            required: true,
        },
    },
    methods: {
        handleClick() {
            this.$emit("add");
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
    color: #0f6cbf;
    border: 1px solid #0f6cbf;
    padding: 15px 10px;
}

.add-activity:hover {
    background-color: #cfe2f1;
    cursor: pointer;
    span,
    p {
        color: #0b4476;
    }

    p {
        text-decoration: underline;
    }
}

.add-activity__plus-icon {
    display: flex;
    justify-content: center;
    align-items: center;
    border: 1px solid #0f6cbf;
    padding: 5px;

    span {
        color: #0f6cbf;
    }
}
</style>
