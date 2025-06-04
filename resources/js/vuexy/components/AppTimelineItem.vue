<template>
    <li
        v-bind="$attrs"
        class="timeline-item"
        :class="[`timeline-variant-${variant}`, fillBorder ? `timeline-item-fill-border-${variant}` : null]"
        v-on="$listeners"
    >
        <div
            v-if="!icon"
            class="timeline-point timeline-point-indicator"
        />
        <div
            v-else
            class="timeline-item-icon d-flex align-items-center justify-content-center rounded-circle"
        >
            <feather-icon :icon="icon" />
        </div>
        <slot>
            <div class="d-flex flex-sm-row flex-column flex-wrap justify-content-between mb-1 mb-sm-0">
                <h6 v-text="title" />
                <small
                class="timeline-item-time text-nowrap text-muted"
                v-text="time"
                />
            </div>
            <p
                class="mb-0"
                v-text="subtitle"
            />
        </slot>
    </li>
</template>
<script>
export default {
    name:'AppTimelineItem',
    props: {
    variant: {
      type: String,
      default: 'primary',
    },
    title: {
      type: String,
      default: null,
    },
    subtitle: {
      type: String,
      default: null,
    },
    time: {
      type: String,
      default: null,
    },
    icon: {
      type: String,
      default: null,
    },
    fillBorder: {
      type: Boolean,
      default: false,
    },
  },
}
</script>
<style  scoped>
.timeline-variant-success .timeline-point {
    background-color: #28c76f !important;
}
.timeline-variant-secondary .timeline-point {
    background-color: #82868b !important;
}
</style>