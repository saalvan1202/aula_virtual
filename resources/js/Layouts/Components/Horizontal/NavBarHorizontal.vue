<template>
<div class="navbar-container d-flex content align-items-center">
    <!-- Nav Menu Toggler -->
    <ul class="nav navbar-nav d-xl-none">
        <li class="nav-item">
            <a class="nav-link" @click="toggleVerticalMenuActive">
                <feather-icon
                    icon="MenuIcon"
                    size="21"
                    style="color:#fff"
                />
            </a>
        </li>
    </ul>
    <ul class="navbar-header nav navbar-nav flex-row d-xl-block d-none" style="position:initial">
         <!-- Logo & Text -->
        <li class="nav-item mr-auto">
            <a class="navbar-brand" :href="routeTo('')">
                <span class="brand-logo">
                    <img  :src="asset('images/logo_unsm.png')"  alt="logo"/>
                </span>
                <h2 class="brand-text mb-0" style="padding-left: 1rem;color:#fff;">{{appName}}</h2>
            </a>
        </li>
    </ul>
    <!-- Left Col -->
    <div class="bookmark-wrapper align-items-center flex-grow-1 d-none d-lg-flex"></div>
    <BNavbarNav class="nav align-items-center ml-auto">
        <UserDropDown/>
    </BNavbarNav>
</div>
</template>
<script>
import {BNavbarNav } from 'bootstrap-vue'
import UserDropDown from "../UserDropDown";
export default{
    name:'NavBarHorizontal',
    components:{BNavbarNav,UserDropDown},
    props:{
        toggleVerticalMenuActive:Function
    },
    computed:{
        appName(){
            return this.$page.props.app_name || ''
        }
    }
}
</script>