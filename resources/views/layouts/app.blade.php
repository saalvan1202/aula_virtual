<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="layout-menu-fixed">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PVirtual</title>

    @include('layouts.styles')
</head>
<body class="instituto-t">
@inertia
<script>
    const baseUrl="{{trim(url('/'),'/')}}";
</script>
<script src="{{asset('js/jquery-3.6.0.min.js') }}" ></script>
<script src="{{asset('js/datatables/jquery.dataTables.min.js') }}" ></script>
<script src="{{asset('js/datatables/datatables.bootstrap4.min.js') }}" ></script>
<script src="{{asset('js/select2.full.min.js')}}"></script>
<script src="{{asset(mix('js/manifest.js')) }}" defer></script>
<script src="{{asset(mix('js/vendor.js')) }}" defer></script>
<script src="{{asset(mix('js/app.js')) }}" defer></script>
</body>
</html>
