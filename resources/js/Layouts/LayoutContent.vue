<template>
    <component :is="layout">
        <template
          v-for="(index, name) in $scopedSlots"
          v-slot:[name]="data"
        >
          <slot
            :name="name"
            v-bind="data"
          />
        </template>
    </component>
</template>

<script>
import LayoutVertical from './Components/Vertical/LayoutVertical.vue';
import LayoutHorizontal from './Components/Horizontal/LayoutHorizontal.vue';
import mixinLayoutContent from './mixinLayoutContent';
export default {
    name: "LayoutContent",
    components: {LayoutVertical,LayoutHorizontal},
    mixins:[mixinLayoutContent],
    created(){
        this.axiosInterceptors();
    },
    computed:{
        layout(){
            return 'LayoutVertical';
            //return 'LayoutHorizontal';
        }
    }

}
</script>

<style scoped>

</style>
