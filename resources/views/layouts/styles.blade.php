<link rel="stylesheet" href="{{ asset('css/vendors.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-extended.css') }}">
<link rel="stylesheet" href="{{ asset('css/colors.css') }}">
<link rel="stylesheet" href="{{ asset('css/components.css') }}">
<link rel="stylesheet" href="{{ asset('css/vertical-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/horizontal-menu.css') }}">
<link rel="stylesheet" href="{{ asset('css/page-auth.css') }}">
<link rel="stylesheet" href="{{ asset('css/jsTree/style.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/vue-select.css') }}">
<style type="text/css">
    [tabindex="-1"]:focus:not(:focus-visible){outline:0!important}
    :focus-visible{outline:0!important}
    table.dataTable tbody tr.selected {
        background-color: rgba(115, 103, 240, 0.05) !important;
        color: #7367f0 !important;
        box-shadow: 0 0 1px 0 #7367f0 !important;
        border-radius: 5px;
    }
    table.dataTable tbody td{
        font-size: 0.9rem;
    }
    .instituto-t nav.header-navbar{
        background: #01b200 !important;
    }
    .instituto-t .user-nav{
        color:#ffffff;
    }


    .feather, [data-feather]{
        height:auto;
        width:auto;
    }

    .b-avatar.badge-light-secondary {
        background-color: rgba(130, 134, 139, 0.12);
        color: #82868b;
    }
    .b-avatar.badge-light-primary {
        background-color: rgba(115, 103, 240, 0.12);
        color: #7367f0;
    }
    .b-avatar.badge-light-info {
        background-color: rgba(0,207,232,.12);
        color: #00cfe8;
    }
    .b-avatar.badge-light-success {
        background-color: rgba(40,199,111,.12);
        color: #28c76f;
    }
    .b-avatar.badge-light-warning {
        background-color: rgba(255, 159, 67, 0.12);
        color: #ff9f43;
    }
    .b-avatar.badge-light-danger {
        background-color: rgba(234, 84, 85, 0.12);
        color: #ea5455;
    }

    .b-table-sticky-header>.table>thead>tr>th {
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .instituto-t .horizontal-menu .header-navbar.navbar-horizontal ul#main-menu-navigation > li.active > a {
        background: linear-gradient(118deg, #01b200, rgba(1, 178, 0, 0.7));
    }
    .instituto-t .main-menu.menu-light .navigation > li ul .active{
        background: linear-gradient(118deg, #01b200, rgba(1, 178, 0, 0.7));
        box-shadow: 0 0 10px 1px rgb(95 214 53 / 70%);
    }
    .instituto-t .nav-pills .nav-link.active{
        background-color: #01b200;
        border-color: #01b200;
    }
    .instituto-t .list-group .list-group-item-action.active {
        background-color: #01b200 !important;
    }
    .instituto-t .list-group .list-group-item-action:active{
        background-color:rgba( 1, 178, 0, 0.1);
        color:#01b200;
    }
    .instituto-t .list-group-item.active{
        border-color:#01b200;
    }
    .instituto-t .main-menu.menu-light .navigation > li.active > a{
        background: linear-gradient(118deg, #01b200, rgba(1, 178, 0, 0.7));
        box-shadow: 0 0 10px 1px rgb(95 214 53 / 70%);
    }
    .form-label{
        font-size: 1rem;
    }
    .sidebar-left{
        width: 250px;
        height: inherit;
        float:left;
        margin-right: .5em;
    }
    .content-right{

        width: 100%;
        float:left;
    }
    @media (min-width: 768px){
        .content-right {
            width: calc(100% - 260px);
        }
    }
    @media (max-width: 768px){
        .sidebar-left{
            width: 100%;
        }
    }
    .option-app-menu{
        margin-bottom: .5em;
    }
    .list-group-item-option{
        padding: 0.6rem 1.25rem;
    }

    .vs__dropdown-toggle {
        padding: 0.59px 0 4px 0 !important;
        transition: all 0.25s ease-in-out;
    }
    .vs--single .vs__dropdown-toggle {
        padding-left: 6px !important;
    }
    .vs--open .vs__dropdown-toggle {
        border-color: #7367f0;
        border-bottom-color: #7367f0;
        border-bottom-left-radius: 0.357rem;
        border-bottom-right-radius: 0.357rem;
        box-shadow: 0 3px 10px 0 rgba(34, 41, 47, 0.1);
    }
    .select-size-lg .vs__dropdown-toggle,
    .select-size-lg .vs__selected {
        font-size: 1.25rem;
    }
    .select-size-lg .vs__dropdown-toggle {
        padding: 5px;
        margin-top: 0;
    }
    .select-size-sm .vs__dropdown-toggle {
        padding-bottom: 0;
        padding: 1px;
    }
    .select-size-sm.vs--single .vs__dropdown-toggle {
        padding: 2px;
    }
    .select-size-sm .vs__dropdown-toggle,
    .select-size-sm .vs__selected {
        font-size: 0.9rem;
    }
    .select-size-sm .vs__actions {
        padding-top: 2px;
        padding-bottom: 2px;
    }
    .select-size-sm .vs__deselect svg {
        vertical-align: middle !important;
    }
    .select-size-sm .vs__search {
        margin-top: 0;
    }
    .select-size-sm.v-select .vs__selected {
        font-size: 0.75rem;
    }
    .select-size-sm.v-select .vs__selected {
        padding: 0 0.3rem;
    }
    .select-size-sm.v-select:not(.vs--single) .vs__selected {
        margin: 4px 5px;
    }
    .select-size-sm.v-select.vs--single .vs__selected {
        margin-top: 1px;
    }
    .select-size-sm.vs--single.vs--open .vs__selected {
        margin-top: 4px;
    }
    .vs__open-indicator {
        fill: none;
        margin-top: 0.15rem;
    }
    .vs__dropdown-option--disabled {
        opacity: 0.5;
    }
    .vs__dropdown-option--disabled.vs__dropdown-option--selected {
        background: #7367f0 !important;
    }
    .vs__dropdown-option {
        color: #6e6b7b;
    }
    .vs__dropdown-option,.vs__no-options {
        padding: 7px 20px;
    }
    .vs__dropdown-option--selected {
        background-color: #7367f0;
        color: #fff;
        position: relative;
    }
    .vs__dropdown-option--selected::after {
        content: "";
        height: 1.1rem;
        width: 1.1rem;
        display: inline-block;
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        right: 20px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23fff' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-check'%3E%3Cpolyline points='20 6 9 17 4 12'%3E%3C/polyline%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: 1.1rem;
    }

    /*.vs__dropdown-option--highlight {
        background: rgba(115, 103, 240, 0.12);
    }*/
    .vs__dropdown-option--highlight {
        background: rgba(115, 103, 240, 0.12) !important;
    }
    .vs__dropdown-option--highlight {
        color: #7367f0 !important;
    }
    .vs__dropdown-option--selected {
        background-color: #7367f0;
        color: #fff !important;
        position: relative;
    }
    .vs__dropdown-option--selected.vs__dropdown-option--highlight {
        color: #fff !important;
        background-color: #7367f0 !important;
    }
    .vs__clear svg {
        color: #6e6b7b;
    }
    .vs__selected {
        /*color: #fff*/;
    }
    .v-select.vs--single .vs__selected {
        color: #6e6b7b;
        transition: transform 0.2s ease;
        margin-top: 5px;
    }
    .vs__selected-options>input{
        padding-left: 0 !important;
    }
    .vs--single.vs--open .vs__selected {
        transform: translateX(5px);
    }
    .vs__selected .vs__deselect {
        color: inherit;
    }
    /*

    .vs__dropdown-option--highlight{
        color: white !important;
        background-color: #7367f0 !important;
    }
    */
    .form-group.is-invalid .v-select .vs__dropdown-toggle {
        border-color: #ea5455;
    }

    .table-fixed{
        table-layout: fixed;
        width: 100%;
    }

    .table-fixed td, .table-fixed th {
        vertical-align: top;
        border-top: 1px solid #ccc;
        padding: 10px;
        width: 100px;
    }

    .table-fixed .fix {
        position: absolute;
        margin-left: -100px;
        width: 100px;
    }

    .table-fixed .inner {
        overflow-x: scroll;
        overflow-y: visible;
        width: 400px;
        margin-left: 100px;
    }
    .flatpickr-confirm.visible {
        max-height: 40px;
    }
    .flatpickr-confirm {
        height: 40px;
        max-height: 0px;
        visibility: hidden;
        display: flex;
        justify-content: center;
        align-items: center;
        cursor: pointer;
        background: #569ff7;
        color:#ffff;
    }
    .flatpickr-confirm path {
        fill: #ffff;
    }​

</style>
