<template>
    <div class="card">
        <div class="card-header">
            <slot name="header" />
        </div>
        <div class="card-content">
            <div class="card-body card-body-vue">
                <slot />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: "CardCourse",
};
</script>
<style scoped>
.card-header {
    min-height: 15vh;
}
</style>
