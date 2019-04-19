<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="l-body">

<header class="l-header">
    <nav class="p-navbar">
        <h1 class="p-navbar__title">
            <a href="#" class="p-navbar__title_link">神ったー</a>
        </h1>

        <a href="#" class="p-navbar__login">ログイン/新規登録</a>
    </nav>
</header>

<main class="l-main">
    <div class="l-contents">

        <div class="p-contents__area">

        </div>
    </div>
</main>

<footer class="l-foot c-foot">
    <p class="c-foot__p">Copyright © 神ったー. All Right Reserved.</p>
</footer>
</body>

</html>