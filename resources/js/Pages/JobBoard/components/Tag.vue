<template>
    <div class="tag" @click="handleClick">
        <p class="m-0 p-0">{{ name }}</p>
        <span><feather-icon icon="XIcon" size="15" stroke-width="2.5" /></span>
    </div>
</template>

<script>
export default {
    props: {
        name: {
            type: String,
            required: true,
        },
    },
    methods: {
        handleClick() {
            this.$emit("click");
        },
    },
};
</script>

<style scoped>
.tag {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    background-color: #0c164f;
    color: white;
    font-weight: 700;
    font-size: 10px;
    padding: 2px 4px 2px 4px;
    border-radius: 4px;
    cursor: pointer;
}
</style>
