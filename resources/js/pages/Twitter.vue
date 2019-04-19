<template>
    <main class="l-main">
        <div class="l-contents">

            <div class="p-contents__area--narrow">
                <h2 class="p-contents__head"><i class="c-icon--twitter fab fa-twitter"></i>利用するTwitterアカウントを選択する</h2>
                <div v-show="isMaximumAccount" class="c-card p-twitter__card">
                    <a href="auth/twitter/oauth">
                        <p class="p-twitter__create">
                            <i class="c-icon--twitter p-twitter__icon--create far fa-plus-square"></i>Twitterアカウントの追加
                        </p>
                    </a>
                </div>
                <ul class="p-twitter">
                    <TwitterCard
                            v-for="user in users"
                            :key="user.id"
                            :item="user"
                    />
                </ul>
            </div>
        </div>
    </main>
</template>

<script>
    import TwitterCard from '../components/TwitterCard.vue'
    import {OK} from '../utility'

    export default {
        components: {
            TwitterCard,
        },
        data() {
            return {
                users: [],
                accountNum: 0
            }
        },
        methods: {
            async fetchTwitterUsers() {
                const response = await axios.get('/api/twitter/user/list')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.users = response.data.twitter_accounts
                this.accountNum = response.data.account_num
                // // console.log(users);
                // // console.log(accountNum);

            },
        },
        computed: {
            isMaximumAccount () {
                return this.accountNum <= 10
            }
        },
        created() {
            this.fetchTwitterUsers()
        },
    }
</script>

<style scoped>

</style>