<template>
    <li
        class="nav-item"
        :class="{
            active: isActive,
        }"
    >
        <a class="d-flex align-items-center" @click.prevent="menuTo(item)">
            <feather-icon :icon="item.icon || 'CircleIcon'" />
            <span class="menu-title text-truncate">{{ item.title }}</span>
        </a>
    </li>
</template>

<script>
import { router } from "@inertiajs/vue2";
import useVerticalNavMenuLink from "../../../../vuexy/useVerticalNavMenuLink";
import { routeTo } from "../../../../helpers.js";
export default {
    name: "VerticalNavMenuLink",
    props: {
        item: Object,
        routeChanged: String,
    },
    setup(props, context) {
        const { isActive, linkProps, updateIsActive } = useVerticalNavMenuLink(
            props.item
        );
        const menuTo = (item) => {
            router.visit(routeTo(item.route));
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

<style scoped></style>
