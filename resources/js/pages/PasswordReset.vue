<template>
    <div class="l-contents">

        <div class="p-contents__area--narrow">
            <section class="c-tab">
                <ul class="c-tab__list">
                    <li class="c-tab__item c-tab__item--active">パスワード再設定</li>
                </ul>
            </section>

            <section class="p-login">
                <div class="c-panel p-login__panel u-color__bg--white">
                    <form class="p-form" @submit.prevent="resetPassword">
                        <div v-if="errors" class="p-form__errors">
                            <ul v-if="errors.email">
                                <li v-for="msg in errors.email" :key="msg">{{ msg }}</li>
                            </ul>
                            <ul v-if="errors.password">
                                <li v-for="msg in errors.password" :key="msg">{{ msg }}</li>
                            </ul>
                            <p v-if="errors.message">{{ errors.message }}</p>
                        </div>
                        <label class="p-form__label" for="reset-email">メールアドレス</label>
                        <input type="email" class="p-form__item" id="reset-email"
                               placeholder="sample@kamitter.ltd" required
                               v-model="resetForm.email"
                        >
                        <label class="p-form__label" for="reset-password">パスワード</label>
                        <input type="password" class="p-form__item" id="reset-password"
                               placeholder="半角英数8文字以上" minlength="8" required
                               v-model="resetForm.password"
                        >
                        <label class="p-form__label" for="reset-password_confirmed">パスワード（確認）</label>
                        <input type="password" class="p-form__item" id="reset-password_confirmed"
                               minlength="8" required
                               v-model="resetForm.password_confirmation"
                        >
                        <p v-show="isReseted">パスワードが変更されました。</p>
                        <div class="p-form__button">
                            <button type="submit"
                                    class="c-button c-button--inverse"
                            >ログイン
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </div>
    </div>
</template>

<script>
    import {OK, UNPROCESSABLE_ENTRY} from '../utility'
    import router from '../router'

    export default {
        data() {
            return {
                isReseted: false,
                errors: null,
                resetForm: {
                    email: '',
                    password: '',
                    password_confirmation: '',
                    token: '',
                }
            }
        },
        props: {
            token: {
                type: String,
                required: false,
            },
        },
        methods: {
            /**
             * パスワードリマインダーAPIを使用して、パスワードにリセットを行う
             * リセットが成功したら、メッセージを表示してログイン画面に遷移させる
             */
            async resetPassword() {
                this.clearErrors()
                // パスワードリセットAPIを呼び出す
                const response = await axios.post('/api/password/reset', this.resetForm)

                if (response.status === UNPROCESSABLE_ENTRY) {
                    //バリデーションエラー
                    this.errors = response.data.errors
                    return false
                } else if (response.status !== OK) {
                    //システムエラー類
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$set(this, 'isReseted', 'true')
                this.clearResetForm()

                setTimeout(function () {
                    router.push('/login')
                }, 3000)

            },

            /**
             * Propsで渡されたURLのGETパラメータ、tokenをデータに格納する
             */
            setTokenFromUrlParam() {
                this.resetForm.token = this.token
            },
            /**
             * フォーム項目を空にする
             */
            clearResetForm() {
                this.resetForm.email = ''
                this.resetForm.password = ''
                this.resetForm.password_confirmation = ''
            },
            /**
             * エラーメッセージのリセット
             */
            clearErrors() {
                this.errors = null
            }
        },
        created() {
            this.setTokenFromUrlParam()
        }
    }
</script>