<template>
    <li
        :class="{
            active: isActive,
        }"
    >
        <a class="dropdown-item" @click.prevent="menuTo(item)">
            <feather-icon :icon="item.icon || 'CircleIcon'" />
            <span class="menu-title text-truncate">{{ item.title }}</span>
        </a>
    </li>
</template>
<script>
import useVerticalNavMenuLink from "../../../../vuexy/useVerticalNavMenuLink";
import { routeTo } from "../../../../helpers.js";
export default {
    name: "HorizontalNavMenuLink",
    props: {
        item: Object,
        routeChanged: String,
    },
    setup(props, context) {
        const { isActive, linkProps, updateIsActive } = useVerticalNavMenuLink(
            props.item
        );
        const menuTo = (item) => {
            context.root.$inertia.visit(routeTo(item.route));
        };
        return {
            isActive,
            linkProps,
            updateIsActive,
            menuTo,
        };
    },
    watch: {
        /*$route: {
            immediate: true,
            handler:function(value){
                this.updateIsActive()
            }
        },*/
        routeChanged: {
            immediate: true,
            handler: function (value) {
                this.updateIsActive();
            },
        },
    },
};
</script>
