<template>
    <main class="l-main">
        <div class="l-contents">

            <div class="p-contents__area--narrow">
                <section class="p-tab">
                    <ul class="p-tab__list">
                        <li class="p-tab__item p-tab__item--active">ログイン</li>
                        <li class="p-tab__item">新規登録</li>
                    </ul>
                </section>

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
                                      >
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
</template>

<script>
    export default {
    }
</script>