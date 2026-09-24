<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
{{--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    @vite('src/main.js')
    @inertiaHead
</head>
<body  class="general-body">
@inertia
</body>
</html>
