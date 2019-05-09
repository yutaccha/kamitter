<template>
    <div class="l-contents">

        <div class="p-contents__area--narrow">
            <h2 class="p-contents__head"><i class="c-icon--twitter fab fa-twitter"></i>利用するTwitterアカウントを選択する</h2>
            <div v-show="isMaximumAccount" class="c-card p-twitter__card u-color__bg--white">
                <a href="auth/twitter/oauth">
                    <p class="p-twitter__create">
                        <i class="c-icon--twitter p-twitter__icon--create far fa-plus-square"></i>Twitterアカウントの追加
                    </p>
                </a>
            </div>
            <ul class="p-twitter">
                <transition-group name="t-twitter_card">
                    <TwitterCard
                            v-for="(user, index) in users"
                            :key="user.id"
                            :item="user"
                            :index="index"
                            @delUser="removeCard"
                    />
                </transition-group>
            </ul>
        </div>
    </div>
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
            /**
             * ユーザーが登録しているTwitterUserのID一覧を取得する
             */
            async fetchTwitterUsers() {
                const response = await axios.get('/api/twitter/user/list')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.users = response.data.twitter_accounts
                this.accountNum = response.data.account_num

            },
            /**
             * TwitterCardのemitをトリガーにして
             * TwitterUserのカードを配列から削除する
             */
            removeCard(emitObject) {
                this.users.splice(emitObject.index, 1)
            }
        },
        computed: {
            //TwitterUserアカウントを追加するボタンの非表示フラグ
            isMaximumAccount() {
                return this.accountNum < 10
            }
        },
        //ページ作成時に実行
        async created() {
            await this.fetchTwitterUsers()
        }
    }
</script>