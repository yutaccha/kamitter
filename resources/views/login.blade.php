<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
            {{--ログイン・新規登録切り替えタブ--}}
            <section class="p-tab">
                <ul class="p-tab__list">
                    <li class="p-tab__item p-tab__item--active">ログイン</li>
                    <li class="p-tab__item">新規登録</li>
                </ul>
            </section>

            {{--入力フォームパネル--}}
            <section class="p-login">
                <div class="p-login__panel">
                    <form class="p-form">
                        <label class="p-form__label" for="login-email">メールアドレス</label>
                        <input type="text" class="p-form__item" id="login-email">
                        <label class="p-form__label" for="login-password">パスワード</label>
                        <input type="password" class="p-form__item" id="login-password">
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--inverse">ログイン</button>
                        </div>
                    </form>
                </div>
                <div class="p-login--panel">
                    <div class="p-login__panel" v-show="tab === 2">
                        <form class="p-form" @submit.prevent="register">
                            <label class="p-form__label" for="username">Name</label>
                            <input type="text" class="p-form__item" id="username">
                            <label class="p-form__label" for="email">Email</label>
                            <input type="text" class="p-form__item" id="email">
                            <label class="p-form__label" for="password">Password</label>
                            <input type="password" class="p-form__item" id="password">
                            <label class="p-form__label" for="password-confirmation">Password (confirm)</label>
                            <input type="password" class="p-form__item" id="password-confirmation"
                                   v-model="registerForm.password_confirmation">
                            <div class="p-form__button">
                                <button type="submit" class="c-button c-button--inverse">登録</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>
</main>

<footer class="l-foot c-foot">
    <p class="c-foot__p">Copyright © 神ったー. All Right Reserved.</p>
</footer>
</body>

</html>