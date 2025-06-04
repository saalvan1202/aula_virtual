import Vue from 'vue';
import {alertError, alertWarning} from "../sweetAlert2";
export default{
    methods:{
        axiosInterceptors(){
            this.$http.interceptors.response.use((response)=>{
                /*let cookieArray = document.cookie.split(";");
                let token='';
                for(var i = 0; i < cookieArray.length; i++) {
                    let cookiePair = cookieArray[i].split("=");
                    if(cookiePair[0].trim() == 'XSRF-TOKEN') {
                        token = decodeURIComponent(cookiePair[1]);
                        this.$http.defaults.headers['X-XSRF-TOKEN'] = token;
                    }
                }*/
                this.onCleanError(response.config);
                return response;
            },(err)=>{
                if (err.response && err.response.status == 422){
                    this.mapErrors(err.response.data.errors,err.response.config);
                }
                if (err.response && err.response.status == 401){
                    alertWarning('Su sesión ha expirado!. Inicie sesión para continuar');
                }
                if (err.response && err.response.status == 419){
                    alertWarning('La pagina expiro,recarga la pagina!!');
                    //return;
                }

                if (err.response && err.response.status == 500){
                    let error='ocurrio un error inesperado';
                    if(this.$page.props.app_debug){
                        error=err.response.data.message+'\n'+err.response.data.file;
                    }
                    alertError(error,2000);
                }
                throw err;
            });
        },
        mapErrors(errors,config){
            const mapErrors={};
            for (let field of Object.keys(errors)) {
                mapErrors[field]=errors[field][0];
            }

            this.setErrors(mapErrors,config);
        },
        onCleanError(config){
            this.setErrors({},config);
        },
        setErrors(errors,config){
            if(config.headers['X-Inertia-Error-Bag']!=undefined){
                let errorBag=config.headers['X-Inertia-Error-Bag'];
                Vue.set(this.$page.props.errors,errorBag, errors);
                return;
            }
            Vue.set(this.$page.props,'errors',errors);
        }
    }
}
