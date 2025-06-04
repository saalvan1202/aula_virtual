import Vue from "vue";
import { createInertiaApp } from "@inertiajs/vue2";
import axios from "axios";
import Vuelidate from "@vuelidate/core";
import "./global-components";
import "./filters";
import globalMixin from "./global-mixin";
import inputUpper from "./inputUpper.js";
import { BootstrapVue, IconsPlugin } from "bootstrap-vue"; // Importa BootstrapVue
import "bootstrap-vue/dist/bootstrap-vue.css"; // Importa CSS de BootstrapVue

//import 'vue-select/dist/vue-select.css';
//Algunas funcionalidades de boostratpVue no estaban agregados, falta estas líneas de código('Components/TableAcordion.vue')
Vue.use(BootstrapVue);
Vue.use(IconsPlugin);
Vue.prototype.$http = axios.create();
Vue.use(Vuelidate);
String.prototype.ucwords = function () {
    var str = this.toLowerCase();
    return str.replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, function (s) {
        return s.toUpperCase();
    });
};
//const element = document.getElementById('app')
createInertiaApp({
    progress: false,
    resolve: (name) => {
        return require(`./Pages/${name}`).default;
    },
    setup({ el, App, props, plugin }) {
        Vue.use(plugin);
        Vue.directive("input-upper", inputUpper);

        Vue.directive("focus", {
            inserted: (ell, binding, vNode) => {
                vNode.context.$nextTick(() => ell.focus());
            },
        });
        Vue.mixin(globalMixin);
        return new Vue({
            render: (h) => h(App, props),
        }).$mount(el);
    },
});
