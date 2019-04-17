<template>
    <nav class="p-navbar">
        <h1 class="p-navbar__title">
            <RouterLink class="p-navbar__title_link" to="/">神ったー</RouterLink>
        </h1>

        <span v-if="isLogin" class="navbar__item">
            {{ username }}<i class="c-icon fas fa-caret-down"></i>
        </span>
        <div v-else class="navbar__item">
            <RouterLink class="button button--link" to="/login">
                ログイン/新規登録
            </RouterLink>
        </div>
    </nav>
</template>

<script>
    export default {
        computed: {
            isLogin () {
                return this.$store.getters['auth/check']
            },
            username () {
                return this.$store.getters['auth/username']
            }
        }
    }
</script>