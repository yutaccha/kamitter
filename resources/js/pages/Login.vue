<template>
    <div class="l-contents">

        <div class="p-contents__area--narrow">
            <section class="c-tab">
                <ul class="c-tab__list">
                    <li
                            class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===1}"
                            @click="tab = 1"
                    >ログイン
                    </li>
                    <li
                            class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===2}"
                            @click="tab = 2"

                    >新規登録
                    </li>
                </ul>
            </section>

            <section class="p-login">
                <transition-group name="tab">
                    <div class="c-panel p-login__panel u-color__bg--white" v-show="tab === 1" key="login">
                        <!--@submit に続く .prevent はイベント修飾子と呼ばれます。.prevent を記述することは、
                        イベントハンドラで event.preventDefault() を呼び出すのと同じ効果があります。-->
                        <form class="p-form" @submit.prevent="login">
                            <div v-if="loginErrors" class="errors">
                                <ul v-if="loginErrors.email">
                                    <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
                                </ul>
                                <ul v-if="loginErrors.password">
                                    <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
                                </ul>
                            </div>
                            <label class="p-form__label" for="login-email">メールアドレス</label>
                            <input type="text" class="p-form__item" id="login-email" v-model="loginForm.email">
                            <label class="p-form__label" for="login-password">パスワード</label>
                            <input type="password" class="p-form__item" id="login-password"
                                   v-model="loginForm.password">
                            <div class="p-form__button">
                                <button type="submit" class="c-button c-button--inverse">ログイン</button>
                            </div>
                        </form>
                    </div>
                    <div class="c-panel p-login__panel u-color__bg--white" v-show="tab === 2" key="register">
                        <form class="p-form" @submit.prevent="register">
                            <div v-if="registerErrors" class="errors">
                                <ul v-if="registerErrors.name">
                                    <li v-for="msg in registerErrors.name" :key="msg">{{ msg }}</li>
                                </ul>
                                <ul v-if="registerErrors.email">
                                    <li v-for="msg in registerErrors.email" :key="msg">{{ msg }}</li>
                                </ul>
                                <ul v-if="registerErrors.password">
                                    <li v-for="msg in registerErrors.password" :key="msg">{{ msg }}</li>
                                </ul>
                            </div>
                            <label class="p-form__label" for="username">ユーザー名</label>
                            <input type="text" class="p-form__item" id="username" v-model="registerForm.name">
                            <label class="p-form__label" for="email">メールアドレス</label>
                            <input type="text" class="p-form__item" id="email" v-model="registerForm.email">
                            <label class="p-form__label" for="password">パスワード</label>
                            <input type="password" class="p-form__item" id="password"
                                   v-model="registerForm.password">
                            <label class="p-form__label" for="password-confirmation">パスワード(確認)</label>
                            <input type="password" class="p-form__item" id="password-confirmation"
                                   v-model="registerForm.password_confirmation">
                            <div class="p-form__button">
                                <button type="submit" class="c-button c-button--inverse">登録</button>
                            </div>
                        </form>
                    </div>
                </transition-group>
            </section>
        </div>
    </div>
</template>

<script>
    import {mapState} from 'vuex'

    export default {
        data() {
            return {
                tab: 1,
                loginForm: {
                    email: '',
                    password: ''
                },
                registerForm: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                }
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus,
                loginErrors: state => state.auth.loginErrorMessages,
                registerErrors: state => state.auth.registerErrorMessages
            })
        },
        methods: {
            async login() {
                // authストアのloginアクションを呼び出す
                await this.$store.dispatch('auth/login', this.loginForm)

                if (this.apiStatus) {
                    // トップページに移動する
                    this.$router.push('/twitter')
                }
            },
            async register() {
                // authストアのresigterアクションを呼び出す
                await this.$store.dispatch('auth/register', this.registerForm)

                if (this.apiStatus) {
                    // トップページに移動する
                    this.$router.push('/twitter')
                }
            },
            clearError() {
                this.$store.commit('auth/setLoginErrorMessages', null)
                this.$store.commit('auth/setRegisterErrorMessages', null)
            }
        },
        created() {
            this.clearError()
        },
    }
</script>