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
                    <li
                            class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===3}"
                            @click="tab = 3"

                    >パスワード再設定
                    </li>
                </ul>
            </section>

            <section class="p-login">
                <transition-group name="tab">
                    <div class="c-panel p-login__panel u-color__bg--white" v-show="tab === 1" key="login">
                        <form class="p-form" @submit.prevent="login">
                            <div v-if="loginErrors" class="p-form__errors">
                                <ul v-if="loginErrors.email">
                                    <li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>
                                </ul>
                                <ul v-if="loginErrors.password">
                                    <li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>
                                </ul>
                            </div>
                            <label class="p-form__label" for="login-email">メールアドレス</label>
                            <input type="email" class="p-form__item" id="login-email"
                                   v-model="loginForm.email" required>
                            <label class="p-form__label" for="login-password">パスワード</label>
                            <input type="password" class="p-form__item" id="login-password"
                                   v-model="loginForm.password" required>
                            <div class="p-form__button">
                                <button type="submit" class="c-button c-button--inverse">ログイン</button>
                            </div>
                        </form>
                    </div>

                    <div class="c-panel p-login__panel u-color__bg--white" v-show="tab === 2" key="register">
                        <form class="p-form" @submit.prevent="register">
                            <div v-if="registerErrors" class="p-form__errors">
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
                            <input type="text" class="p-form__item" id="username"
                                   placeholder="神ったー"
                                   v-model="registerForm.name" required>
                            <label class="p-form__label" for="email">メールアドレス</label>
                            <input type="email" class="p-form__item" id="email"
                                   placeholder="sample@kamitter.ltd"
                                   v-model="registerForm.email" required>
                            <label class="p-form__label" for="password">パスワード</label>
                            <input type="password" class="p-form__item" id="password"
                                   placeholder="半角英数8文字以上"
                                   v-model="registerForm.password">
                            <label class="p-form__label" for="confirm_password">パスワード(確認)</label>
                            <input type="password" class="p-form__item" id="confirm_password"
                                   placeholder=""
                                   v-model="registerForm.password_confirmation" required>
                            <div class="p-form__button">
                                <button type="submit" class="c-button c-button--inverse">登録</button>
                            </div>
                        </form>
                    </div>

                    <div class="c-panel p-login__panel u-color__bg--white" v-show="tab === 3" key="password">
                        <!--@submit に続く .prevent はイベント修飾子と呼ばれます。.prevent を記述することは、
                        イベントハンドラで event.preventDefault() を呼び出すのと同じ効果があります。-->
                        <form class="p-form" @submit.prevent="passwordReset">
                            <div v-if="passwordError" class="p-form__errors">
                                <p>{{ passwordError.message }}</p>
                            </div>
                            <label class="p-form__label" for="login-email">メールアドレス</label>
                            <input type="email" class="p-form__item" id="password-email"
                                   v-model="passwordForm.email" required>
                            <p v-show="showMailMessage">メールを送信しました。メールボックスをご確認ください。</p>
                            <div class="p-form__button">
                                <button type="submit"
                                        class="c-button c-button--inverse"
                                        v-bind:disabled="isPush">メール送信</button>
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
    import {OK, UNPROCESSABLE_ENTRY} from '../utility'

    export default {
        data() {
            return {
                tab: 1,
                showMailMessage: false,
                isPush: false,
                loginForm: {
                    email: '',
                    password: ''
                },
                registerForm: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: ''
                },
                passwordForm: {
                    email: '',
                },
                passwordError: '',
            }
        },
        computed: {
            ...mapState({
                apiStatus: state => state.auth.apiStatus,
                loginErrors: state => state.auth.loginErrorMessages,
                registerErrors: state => state.auth.registerErrorMessages,
            }),
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
                // authストアのregisterアクションを呼び出す
                await this.$store.dispatch('auth/register', this.registerForm)

                if (this.apiStatus) {
                    // トップページに移動する
                    this.$router.push('/twitter')
                }
            },
            async passwordReset() {
                this.$set(this, 'isPush', true)
                this.clearError()
                const response = await axios.post('/api/password/create', this.passwordForm)
                this.$set(this, 'isPush', false)
                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.passwordError = response.data
                    console.log(this.passwordError);
                    return false
                }else if(response.status !== OK){
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.passwordForm.email = ''
                this.$set(this, 'showMailMessage', true)

            },
            clearError() {
                this.$store.commit('auth/setLoginErrorMessages', null)
                this.$store.commit('auth/setRegisterErrorMessages', null)
                this.$set(this, 'passwordError', '')
            }
        },
        created() {
            this.clearError()
        },
    }
</script>