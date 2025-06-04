<template>
    <ul
    v-bind="$attrs"
    class="timeline"
    v-on="$listeners"
  >
    <slot />
  </ul>
</template>
<script>
export default{
    name:'AppTimeline'
}
</script>