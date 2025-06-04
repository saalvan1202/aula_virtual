<template>
    <div class="navbar-container main-menu-content">
        <HorizontalMainMenu :menu-items="navMenuItems"/>
    </div>
</template>
<script>
import HorizontalMainMenu from './Menu/HorizontalMainMenu.vue';
export default{
    name:'HorizontalNavMenu',
    components:{HorizontalMainMenu},
    computed:{
        navMenuItems(){
            return this.$page.props.menus||[];
        }
    }
}
</script>