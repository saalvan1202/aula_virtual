<template>
    <ul
        id="main-menu-navigation"
        class="nav navbar-nav"
        v-show="showMenu"
    >
        <component
            :is="resolveNavComponent(item)"
            v-for="item in menuItems"
            :key="item.id || item.title"
            :item="item"
            :routeChanged="routeChanged"
        />
    </ul>
</template>
<script>
import HorizontalNavMenuGroup from './HorizontalNavMenuGroup.vue';
import HorizontalNavMenuLink from './HorizontalNavMenuLink.vue';
import {currentUrl} from '../../../../helpers.js'
export default {
    name:'HorizontalMainMenu',
    components:{HorizontalNavMenuGroup,HorizontalNavMenuLink},
    data(){
        return {
            routeChanged:'',
            showMenu:true,
        }
    },
    props:{
        menuItems:Array
    },
    mounted(){

        this.$inertia.on('navigate', (event) => {
            this.routeChanged = event.detail.page.url;
        })
        if(currentUrl()==''){
            this.showMenu=false;
        }
    },
    methods:{
        resolveNavComponent(item){
            if (item.header) return 'horizontal-nav-menu-header'
            if (item.children!=undefined && item.children.length>0) return 'horizontal-nav-menu-group'
            return 'horizontal-nav-menu-link'
        }
    }
}
</script>
