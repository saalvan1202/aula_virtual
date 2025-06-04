<template>
    <div class="sidebar-left">
        <div class="sidebar">
            <div class="sidebar-content todo-sidebar">
                <div class="option-app-menu">
                    <VuePerfectScrollbar
                        class="sidebar-menu-list scroll-area"
                        :settings="{
                            maxScrollbarLength: 60,
                        }"
                    >
                        <div style="margin-bottom: 0.7rem !important;" class="border-bottom-secondary px-2 d-flex justify-content-between">
                            <h6 class="section-label  text-secondary" style="font-weight: 600;">
                                Opciones
                            </h6>
                        </div>
                        <div class="list-group list-group-filters">
                            <a class="list-group-item list-group-item-option list-group-item-action"
                               @click.prevent="activeTab('Module')"
                               :class="{
                                    'active':active=='Module'
                                }"
                            >
                                <feather-icon
                                    icon="ClipboardIcon"
                                    size="18"
                                    class="mr-75"
                                />
                                <span class="align-text-bottom line-height-1">Modulos</span>
                            </a>
                            <a class="list-group-item list-group-item-option list-group-item-action"
                               @click.prevent="activeTab('Competence')"
                               :class="{
                                    'active':active=='Competence'
                                }"
                            >
                                <feather-icon
                                    icon="ApertureIcon"
                                    size="18"
                                    class="mr-75"
                                />
                                <span class="align-text-bottom line-height-1">Competencias</span>
                            </a>
                            <a class="list-group-item list-group-item-option list-group-item-action"
                               @click.prevent="activeTab('Course')"
                               :class="{
                                    'active':active=='Course'
                                }"
                            >
                                <feather-icon
                                    icon="BookIcon"
                                    size="18"
                                    class="mr-75"
                                />
                                <span class="align-text-bottom line-height-1">Unidades Didácticas</span>
                            </a>
                        </div>
                    </VuePerfectScrollbar>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar'
export default{
    name:'Option',
    components:{VuePerfectScrollbar},
    data(){
        return{
            active:'',
        }
    },
    methods:{
        plan(){
            return this.$page.props.plan || {};
        },
        activeTab(tab){
            this.active=tab;
            this.$emit('active',tab)
        },
        setActive(tab){
            this.active=tab;
        }
    },
    mounted(){
        this.$emit('active', 'Module')
    }
}
</script>
<style scoped>



</style>
