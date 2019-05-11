<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ asset(mix('css/app.css')) }}" rel="stylesheet">
    <!-- Scripts -->
    <script src="{{ asset(mix('js/app.js')) }}" defer></script>

</head>
<body>
<div id="app"></div>
{{--<a href="{{ route('twitter.provide') }}">認証リンクはこちら</a>--}}
</body>
</html>
