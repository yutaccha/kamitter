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
                        <!--@submit に続く .prevent はイベント修飾子と呼ばれます。.prevent を記述することは、
                        イベントハンドラで event.preventDefault() を呼び出すのと同じ効果があります。-->
                        <form class="p-form" @submit.prevent="resetPassword">
                            <!--<div v-if="loginErrors" class="errors">-->
                                <!--<ul v-if="loginErrors.email">-->
                                    <!--<li v-for="msg in loginErrors.email" :key="msg">{{ msg }}</li>-->
                                <!--</ul>-->
                                <!--<ul v-if="loginErrors.password">-->
                                    <!--<li v-for="msg in loginErrors.password" :key="msg">{{ msg }}</li>-->
                                <!--</ul>-->
                            <!--</div>-->
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
                                >ログイン</button>
                            </div>
                        </form>
                    </div>
            </section>
        </div>
    </div>
</template>

<script>
    import {OK} from '../utility'
    import router from '../router'

    export default {
        data() {
            return {
                isReseted: false,
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
            async resetPassword(){
                const response = await axios.post('/api/password/reset', this.resetForm)
                if(response.status !== OK){
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$set(this, 'isReseted' , 'true')
                this.clearResetForm()

                setTimeout(function () {
                    router.push('/login')
                }, 3000)

            },
            setTokenFromUrlParam(){
                this.resetForm.token = this.token
            },
            clearResetForm(){
                this.resetForm.email = ''
                this.resetForm.password = ''
                this.resetForm.password_confirmation = ''
            },
        },
        created() {
            this.setTokenFromUrlParam()
        }
    }
</script>

<style scoped>

</style>