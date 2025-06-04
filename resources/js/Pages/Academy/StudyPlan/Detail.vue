<template>
    <LayoutContent>
        <BCard no-body>
            <div class="card-header">
                <h4 class="card-title text-center">
                    Datos generales
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <strong>Programa de estudios:</strong>
                        <span>{{plan.programa_estudio.descripcion}}</span>
                    </div>
                    <div class="col">
                        <strong>Plan Estudio:</strong>
                        <span>{{plan.descripcion}}</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <strong>Modalidad:</strong>
                        <span>{{plan.modalidad}}</span>
                    </div>
                </div>
            </div>
        </BCard>
        <Option ref="option" @active="onOption" />
        <div class="content-right">
            <div class="content-wrapper">
                <div class="content-body">
                    <component
                        :is="tab"
                    >
                        <slot/>
                    </component>
                </div>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import LayoutContent from "../../../Layouts/LayoutContent.vue";
import Module from "./Module.vue";
import Competence from './Competence.vue'
import Course from './Course.vue';
import {BCard} from "bootstrap-vue";
import Option from "./Option.vue";
import {eventBus} from "../../../helpers";

export  default {
    name:'ModuleIndex',
    components: {Option, BCard, LayoutContent,Module,Competence,Course},
    props:{
        plan:Object,
        tabActive:String,
    },
    data(){
        return{
            tab:''
        }
    },
    created() {
        this.tab=this.tabActive;
        eventBus.$on('option:active',(tab)=>{
            this.tab=tab;
            this.$refs.option.setActive(this.tab);
        })

    },
    mounted(){
        this.$refs.option.setActive(this.tab);
       /* this.$nextTick(()=>{
            eventBus.$emit('children:navigate','planes_estudios');
        })*/

    },
    methods:{
        onOption(tab){
            this.tab=tab;
        }
    }
}
</script>
