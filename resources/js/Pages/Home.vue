<template>
    <LayoutContent>
        <div class="row">
            <div class="col-md-12">
                <Card
                    class="text-center"
                    style="cursor:pointer"
                    @click.native="accessTo(card)"
                >
                    <h4 class="mb-25 font-weight-bolder">
                        Bienvenido a la plataforma virtual
                    </h4>
                    <div class="alert alert-success" v-if="status">
                        <div class="alert-body">
                            <strong>{{status}}</strong>
                        </div>
                    </div>
                    <!--<BImg style="margin-top: 1em" width="250px" thumbnail fluid :src="asset('images/red.png')" alt="Image 1"/>-->
                    <span style="margin-top: 1em;display: block"></span>
                </Card>
            </div>
        </div>
    </LayoutContent>
</template>
<script>
import {BAvatar,BAlert,BImg} from 'bootstrap-vue';
import _ from 'lodash';
import LayoutContent from '../Layouts/LayoutContent.vue';
import Card from '../Components/Card.vue'
export default{
    components: { LayoutContent, BAvatar,Card,BAlert,BImg },
    name:'Home',
    props:{
        status:String
    },
    methods:{
        accessTo(item){
            this.$inertia.post(this.routeTo(`accesos/${item.id}/sistemas`),{});
        }
    },
    computed:{
        errors(){
            return this.$page.props.errors ||{};
        },
    }
}
</script>
