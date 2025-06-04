<template>
    <li
        class="dropdown dropdown-submenu"
        :class="{
            'show': isOpen,
            'disabled': item.disabled,
            'sidebar-group-active active open': isActive,
            'openLeft': openChildDropdownOnLeft
        }"
        @mouseenter="() => updateGroupOpen(true)"
        @mouseleave="() => updateGroupOpen(false)"
    >
        <a 
            class="dropdown-item"
            href="#"
            :class="{'dropdown-toggle': item.children}"
            @click="() => updateGroupOpen(!isOpen)"

        >
            <feather-icon :icon="item.icon || 'CircleIcon'" />
            <span class="menu-title text-truncate">{{ item.title }}</span>
        </a>
        <ul
            ref="refChildDropdown"
            class="dropdown-menu"
        >
            <component
                :is="resolveNavItemComponent(child)"
                v-for="child in item.children"
                :key="child.id || child.title"
                ref="groupChild"
                :item="child"
                :routeChanged="routeChanged"
            />
        </ul>

    </li>
</template>
<script>
import HorizontalNavMenuLink from './HorizontalNavMenuLink.vue';
import useHorizontalNavMenuGroup from "../../../../vuexy/useHorizontalNavMenuGroup.js";
import {resolveHorizontalNavMenuItemComponent as resolveNavItemComponent} from "../../../../vuexy/utils";
export default{
    name:'HorizontalNavMenuGroup',
    components:{HorizontalNavMenuLink},
    props:{
        item:Object,
        routeChanged:String
    },
    setup(props) {
        const {
            refChildDropdown,
            isActive,
            isOpen,
            updateGroupOpen,
            updateIsActive,
            openChildDropdownOnLeft,
        } = useHorizontalNavMenuGroup(props.item)
        return{
            refChildDropdown,
            openChildDropdownOnLeft,
            resolveNavItemComponent,
            isOpen,
            isActive,
            updateGroupOpen,
            updateIsActive,
        }
    },
    watch:{
        /*$route: {
            immediate: true,
            handler:function(value){
                this.updateIsActive()
            }
        },*/
        routeChanged:{
            immediate: true,
            handler:function(value){
                this.updateIsActive()
            } 
        }
    },
}
</script>