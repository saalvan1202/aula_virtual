<template>
    <div>
        <div class="blockUI"></div>
        <div class="blockUI blockOverlay">
        </div>
        <div class="blockUI blockMsg blockElement">
            <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Spinner"
    }
</script>

<style scoped>
    .blockOverlay{
        z-index: 1000;
        border: none;
        margin: 0px;
        padding: 0px;
        width: 100%;
        height: 100%;
        top: 0px;
        left: 0px;
        background-color: rgb(255, 255, 255);
        opacity: 0.6;
        position: absolute;
    }
    .blockElement{
        position: absolute;
        padding: 0px;
        margin: 0 auto;
        width: 100%;
        top: 50%;
        left: 0;
        text-align: center;
        color: rgb(0, 0, 0);
        border: 0px;
    }
</style>
