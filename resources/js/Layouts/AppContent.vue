<template>
    <div class="app-content content">
        <div class="content-overlay" />
        <div class="header-navbar-shadow" />
        <div class="content-wrapper">
            <!-- breadcrumb -->
            
            <slot name="breadcrumb"></slot>
            <!-- breadcrumb -->
            <div class="content-body">
                <slot/>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "AppContent",
}
</script>

<style scoped>

</style>
