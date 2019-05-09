<template>
    <nav class="p-navbar">
        <h1 class="p-navbar__title">
            <RouterLink class="p-navbar__title_link" to="/">神ったー</RouterLink>
        </h1>

        <div class="p-navbar__menu"
             @click="isMenuActive = ! isMenuActive"
             v-if="isLogin">
            <div class="p-navbar__name">
                {{ username }}<i class="c-icon fas fa-caret-down"></i>
            </div>
            <ul class="p-navbar__list"
                :class="{'p-navbar__list--active': isMenuActive}"
            >
                <li class="p-navbar__item" v-on:click="changeTwitterUser">
                    アカウント切り替え
                </li>
                <li class="p-navbar__item" v-on:click="logout">
                    ログアウト
                </li>
            </ul>
        </div>
        <div v-else class="p-navbar__menu">
            <RouterLink class="button button--link" to="/login">
                ログイン/新規登録
            </RouterLink>
        </div>
    </nav>
</template>

<script>
    export default {
        data() {
            return {
                isMenuActive: false,
            }
        },
        computed: {
            isLogin() {
                return this.$store.getters['auth/check']
            },
            username() {
                return this.$store.getters['auth/username']
            },
            apiStatus() {
                return this.$store.state.auth.apiStatus
            },
        },
        methods: {
            /**
             * ユーザーログアウトしてログイン画面に遷移する
             */
            async logout() {
                await this.$store.dispatch('auth/logout')
                if (this.apiStatus) {
                    this.$router.push('/')
                }
            },
            /**
             * storeからTwitterUserIdを削除して、
             *TwitterUser選択画面に遷移する
             */
            async changeTwitterUser() {
                await this.$store.dispatch('auth/twitterUserLogout')
                if (this.apiStatus) {
                    this.$router.push('/twitter')
                }
            },
        },
    }
</script>