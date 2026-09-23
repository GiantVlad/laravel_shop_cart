<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin WG-Shop</title>
    @vite('src/main.js')
</head>
<body class="general-body">
    <div class="admin-login">
        <div class="admin-login__card">
            <p class="admin-eyebrow">WG Shop</p>
            <h1 style="margin: 0 0 1.25rem; font-size: 1.4rem;">{{ $title ?? 'Admin' }}</h1>

            @if (session('status'))
                <div class="admin-alert admin-alert--success">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="admin-alert admin-alert--danger">
                    <ul style="margin: 0; padding-left: 1rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>
</body>
</html>
