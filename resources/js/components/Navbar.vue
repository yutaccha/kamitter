<template>
    <nav class="p-navbar">
        <h1 class="p-navbar__title">
            <RouterLink class="p-navbar__title_link" to="/twitter">twitter</RouterLink>
        </h1>

        <div class="p-navbar__item" v-if="isLogin">
        <span  class="p-navbar__item">
            {{ username }}<i class="c-icon fas fa-caret-down"></i>
        </span>
            <span class="p-navbar__item" v-on:click.stop="changeTwitterUser">
                アカウント切り替え
            </span>
            <span class="p-navbar__item" v-on:click.stop="logout">
                ログアウト
            </span>
        </div>
        <div v-else class="p-navbar__item">
            <RouterLink class="button button--link" to="/login">
                ログイン/新規登録
            </RouterLink>
        </div>
    </nav>
</template>

<script>
    export default {
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
            async logout() {
                await this.$store.dispatch('auth/logout')
                if (this.apiStatus){
                    this.$router.push('/')
                }
            },
            async changeTwitterUser() {
                await this.$store.dispatch('auth/twitterUserLogout')
                if (this.apiStatus) {
                    this.$router.push('/twitter')
                }
            }
        }
    }
</script>