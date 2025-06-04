import Vue from 'vue'
import FeatherIcon from "./vuexy/components/FeatherIcon";
import VerticalNavMenuLink from "./Layouts/Components/Vertical/Menu/VerticalNavMenuLink.vue";
import VerticalNavMenuGroup from "./Layouts/Components/Vertical/Menu/VerticalNavMenuGroup.vue";

Vue.component(FeatherIcon.name, FeatherIcon)
//Vue.component('VerticalNavMenuHeader', VerticalNavMenuHeader)
Vue.component('VerticalNavMenuGroup', VerticalNavMenuGroup)
Vue.component('VerticalNavMenuLink', VerticalNavMenuLink)
