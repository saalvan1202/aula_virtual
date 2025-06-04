import Vue from 'vue';
import {convertDate} from './helpers';
Vue.filter('convertDate',(_string,_caracterSplit, _caracterJoin)=>{
    return convertDate(_string,_caracterSplit, _caracterJoin);
})