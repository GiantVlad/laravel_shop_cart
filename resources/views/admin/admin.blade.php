<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin WG-Shop</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body style="padding-top: 70px;">
<div id="app">
    @if (Auth::guard('admin')->check())
        @include('admin.nav')
    @endif
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                @yield('left-column')
            </div>
            <div class="col-lg-10 content">
                @yield('content')
            </div>
        </div>
    </div>

    <hr/>
    <footer class="footer">
        <p class="text-center">Created by Uladzimir Sadkou hofirma@gmail.com</p>
    </footer>
</div>
<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<!--
<script src="{{asset('js/admin_script.js')}}"></script>
-->
</body>
</html>