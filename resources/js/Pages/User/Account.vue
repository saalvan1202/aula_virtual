<template>
    <LayoutContent>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">
                    Cuenta
                </h4>
            </div>
            <div class="card-content">
                <div class="card-body card-body-vue">
                    <BTabs
                        vertical
                        content-class="col-12 col-md-9 mt-1 mt-md-0"
                        pills
                        nav-wrapper-class="col-md-3 col-12"
                        nav-class="nav-left"
                    >
                        <BTab active>
                            <template #title>
                                <feather-icon
                                    icon="UserIcon"
                                    size="18"
                                    class="mr-50"
                                />
                                <span class="font-weight-bold">General</span>
                            </template>
                            <AccountGeneral :user="user"/>
                        </BTab>
                        <BTab>
                            <template #title>
                                <feather-icon
                                    icon="LockIcon"
                                    size="18"
                                    class="mr-50"
                                />
                                <span class="font-weight-bold">Cambiar Contraseña</span>
                            </template>
                            <AccountPassword/>
                        </BTab>
                    </BTabs>
                </div>
            </div>
        </div>
    </LayoutContent>
</template>

<script>
import { BTabs, BTab } from 'bootstrap-vue'
import LayoutContent from "../../Layouts/LayoutContent";
import AccountGeneral from "./AccountGeneral";
import AccountPassword from './AccountPassword.vue';
export default {
    name: "Account",
    props:{
        user:Object
    },
    components: {AccountGeneral, LayoutContent,BTabs,BTab, AccountPassword}
}
</script>

<style scoped>

</style>
