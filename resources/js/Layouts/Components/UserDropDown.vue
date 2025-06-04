<template>
    <BNavItemDropdown
        right
        toggle-class="d-flex align-items-center dropdown-user-link"
        class="dropdown-user"
    >
        <template #button-content>
            <div class="d-sm-flex d-none user-nav">
                <p class="user-name font-weight-bolder mb-0">
                    {{user.nombres}}
                </p>
                <span class="user-status">{{user.perfil}}</span>
            </div>
            <span class="avatar">
                <img class="round" :src="asset('images/no_usuario.png')" alt="avatar" height="40" width="40">
                <span class="avatar-status-online"></span>
            </span>
        </template>
        <BDropdownItem @click.prevent="account">
            <feather-icon
                size="16"
                icon="UserIcon"
                class="mr-50"
            />
            <span>Cuenta</span>
        </BDropdownItem>
        <div class="dropdown-divider"></div>
        <BDropdownItem @click.prevent="logout">
            <feather-icon
                size="16"
                icon="LogOutIcon"
                class="mr-50"
            />
            <span>Cerrar Sesion</span>
        </BDropdownItem>
    </BNavItemDropdown>
</template>

<script>
import {BNavItemDropdown,BDropdownItem} from 'bootstrap-vue'
export default {
    name: "UserDropDown",
    components:{
        BNavItemDropdown,
        BDropdownItem
    },
    methods:{
        logout(){
            this.$inertia.post(this.routeTo('logout'))
        },
        account(){
            this.$inertia.visit(this.routeTo('usuarios/cuenta'))
        },
        credentials(){
            this.$inertia.visit(this.routeTo('credenciales'))
        }
    },
    computed:{
        user(){
            return this.$page.props.auth.user ||{}
        }
    }
}
</script>

<style scoped>

</style>
