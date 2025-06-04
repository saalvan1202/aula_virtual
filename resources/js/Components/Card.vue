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
    name: "Card",
};
</script>
<style scoped></style>
