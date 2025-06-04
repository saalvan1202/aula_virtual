<template>
    <small class="text-danger" v-if="error">{{ error }}</small>
</template>
<script>
export default {
    name: "InputError",
    props: {
        error: String,
    },
};
</script>
