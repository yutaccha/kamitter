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

        <div class="p-contents__area--narrow">
            <h2 class="p-contents__head"><i class="c-icon--twitter fab fa-twitter"></i>利用するTwitterアカウントを選択する</h2>
            <ul class="p-twitter">
                <li class="c-card p-twitter__card">
                    <p class="p-twitter__create">
                        <i class="c-icon--twitter p-twitter__icon--create far fa-plus-square"></i>Twitterアカウントの追加
                    </p>
                </li>
                <li class="c-card p-twitter__card">
                    <div class="p-twitter__profile">
                        <figure class="p-twitter__img">
                            <img src="" alt="">
                        </figure>
                        <div class="p-twitter__ids">
                            <p class="p-twitter__id">@Front1111</p>
                            <p class="p-twitter__name">ふふふろん</p>
                        </div>
                    </div>
                    <div class="p-twitter__action">
                        <i class="c-icon--gray p-twitter__icon fas fa-trash-alt"></i>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</main>

<footer class="l-foot c-foot">
    <p class="c-foot__p">Copyright © 神ったー. All Right Reserved.</p>
</footer>
</body>

</html>