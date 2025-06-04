<template>
    <li
        class="nav-item has-sub"
        :class="{
            'open': isOpen,
            'sidebar-group-active': isActive,
        }"
    >
        <a class="d-flex align-items-center" @click="() => updateGroupOpen(!isOpen)">
            <feather-icon :icon="item.icon || 'CircleIcon'" size="13" />
            <span class="menu-title text-truncate">{{ item.title }}</span>
        </a>
        <BCollapse
            v-model="isOpen"
            class="menu-content"
            tag="ul"
        >
            <component
                :is="resolveNavItemComponent(child)"
                v-for="child in item.children"
                :key="child.id || child.title"
                ref="groupChild"
                :item="child"
                :routeChanged="routeChanged"
            />
        </BCollapse>

    </li>
</template>

<script>
import {BCollapse} from 'bootstrap-vue'
import VerticalNavMenuLink from './VerticalNavMenuLink';
import useVerticalNavMenuGroup from "../../../../vuexy/useVerticalNavMenuGroup";
import {resolveVerticalNavMenuItemComponent as resolveNavItemComponent} from "../../../../vuexy/utils";
export default {
    name: "VerticalNavMenuGroup",
    //inject: ['openGroups'],
    components:{
        BCollapse,
        VerticalNavMenuLink
    },
    props:{
        item:Object,
        routeChanged:String
    },
    setup(props){
        const {
            isOpen,
            isActive,
            updateGroupOpen,
            updateIsActive,
        } = useVerticalNavMenuGroup(props.item)
        return{
            isOpen,
            isActive,
            updateGroupOpen,
            updateIsActive,
            resolveNavItemComponent
        }
    },
    watch:{

        routeChanged:{
            immediate: true,
            handler:function(value){
                this.updateIsActive()
            }
        }
    }
}
</script>

<style scoped>

</style>
