<template>
    <div class="l-contents">
        <div class="p-contents__area">
            <section class="p-profile">
                <figure>
                    <img :src="twitterUser.thumbnail" alt="" class="p-profile__img">
                </figure>
                <div class="p-profile__info">
                    <p class="p-profile__name">{{twitterUser.name}}</p>
                    <p class="p-profile__screen">@{{twitterUser.screen_name}}</p>
                    <p class="p-profile__follow">
                        フォロー: <span class="p-profile__number">{{twitterUser.follows}}</span>
                        フォロワー: <span class="p-profile__number">{{twitterUser.followers}}</span>
                    </p>
                </div>
            </section>

            <section class="c-tab">
                <ul class="c-tab__list">
                    <li class="c-tab__item"
                        :class="{'c-tab__item--active': tab ===1}"
                        @click="tab=1">自動フォロー
                    </li>
                    <li class="c-tab__item"
                        :class="{'c-tab__item--active': tab ===2}"
                        @click="tab=2">自動アンフォロー
                    </li>
                    <li class="c-tab__item"
                        :class="{'c-tab__item--active': tab ===3}"
                        @click="tab=3">自動いいね
                    </li>
                    <li class="c-tab__item"
                        :class="{'c-tab__item--active': tab ===4}"
                        @click="tab=4">自動ツイート
                    </li>
                    <li class="c-tab__item"
                        :class="{'c-tab__item--active': tab ===5}"
                        @click="tab=5">キーワード登録
                    </li>
                </ul>
            </section>

            <section class="p-dashboard">
                <transition-group name="t-dashboard_panel" tag="div">

                    <PanelFollow key="follow" v-show="tab===1"></PanelFollow>

                    <PanelUnfollow key="unfollow" v-show="tab===2"></PanelUnfollow>

                    <PanelLike key="like" v-show="tab===3"></PanelLike>

                    <PanelTweet key="tweet" v-show="tab===4"></PanelTweet>

                    <PanelFilter key="filter" v-show="tab===5"></PanelFilter>

                </transition-group>
            </section>

        </div>
    </div>
</template>

<script>
    import {OK} from '../utility'
    import PanelFollow from '../components/PanelFollow'
    import PanelUnfollow from '../components/PanelUnfollow'
    import PanelLike from '../components/PanelLike'
    import PanelTweet from '../components/PanelTweet'
    import PanelFilter from '../components/PanelFilter'

    export default {
        components: {
            PanelFollow,
            PanelUnfollow,
            PanelLike,
            PanelTweet,
            PanelFilter
        },
        data() {
            return {
                tab: 1,
                twitterUser: [],
                refreshFlg: null
            }
        },
        methods: {
            /**
             * 操作するTwitterUserのユーザー情報を取得する
             * profileセクションで表示する
             */
            async fetchTwitterUser() {
                const id = this.$store.state.auth.twitterId
                const response = await axios.get('/api/twitter/user/info/' + id)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.twitterUser = response.data
            },
        },
        async created() {
            this.fetchTwitterUser()
        },
    }
</script>