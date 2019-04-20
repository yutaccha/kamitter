<template>
    <main class="l-main">
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
                            @click="tab=1">自動フォロー</li>
                        <li class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===2}"
                            @click="tab=2">自動アンフォロー</li>
                        <li class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===3}"
                            @click="tab=3">自動いいね</li>
                        <li class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===4}"
                            @click="tab=4">自動ツイート</li>
                        <li class="c-tab__item"
                            :class="{'c-tab__item--active': tab ===5}"
                            @click="tab=5">キーワード登録</li>
                    </ul>
                </section>

                <section class="p-dashboard">
                    <transition-group name="t-dashboard_panel">
                        <div class="c-panel u-color__bg--white" v-show="tab===1" key="1">
                            <div class="c-status">
                                <p class="c-status__show">稼働中</p>
                                <button class="c-status__button c-button c-button--success">サービス開始</button>
                                <button class="c-status__button c-button c-button--danger">停止</button>
                            </div>


                            <div class="c-table__title">
                                <h2 class="c-table__caption">○フォロワーターゲット</h2>
                                <button class="c-button c-button--twitter">
                                    <i class="c-icon c-icon--white fas fa-plus"></i>
                                    フォロワーターゲットを追加
                                </button>
                            </div>
                            <table class="c-table">
                                <tr class="c-table__head">
                                    <th class="c-table__th">ステータス</th>
                                    <th class="c-table__th">ターゲット</th>
                                    <th class="c-table__th">条件</th>
                                    <th class="c-table__th">操作</th>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[実行中]</td>
                                    <td class="c-table__td">@sample1234</td>
                                    <td class="c-table__td">プログラミング Boot</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">@fn103fdsafdasfasdfa1</td>
                                    <td class="c-table__td">プログラミング OR アーキテクチャ</td>
                                    <td class="c-table__td">[待機中]</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">@fteeewitn</td>
                                    <td class="c-table__td">フロントエンジニア 単価 -SES</td>
                                    <td class="c-table__td">[待機中]</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                            </table>
                        </div>

                        <div class="p-dashboard__panel" v-show="tab===2" key="2">
                            <div class="c-status">
                                <p class="c-status__show">稼働中</p>
                                <button class="c-status__button c-button c-button--start">サービス開始</button>
                                <button class="c-status__button c-button c-button--cancel">停止</button>
                            </div>
                        </div>

                        <div class="p-dashboard__panel" v-show="tab===3" key="3">
                            <div class="c-status">
                                <p class="c-status__show">稼働中</p>
                                <button class="c-status__button c-button c-button--start">サービス開始</button>
                                <button class="c-status__button c-button c-button--cancel">停止</button>
                            </div>

                            <table class="c-table">
                                <caption class="c-table__caption">○いいね設定</caption>
                                <tr class="c-table__head">
                                    <th class="c-table__th">条件</th>
                                    <th class="c-table__th">操作</th>
                                </tr>
                                <tr>
                                    <td class="c-table__td">プログラミング Boot</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                            </table>
                        </div>

                        <div class="p-dashboard__panel" v-show="tab===4" key="4">
                            <div class="c-status">
                                <p class="c-status__show">稼働中</p>
                                <button class="c-status__button c-button c-button--start">サービス開始</button>
                                <button class="c-status__button c-button c-button--cancel">停止</button>
                            </div>

                            <table class="c-table">
                                <caption class="c-table__caption">○自動ツイートリスト</caption>
                                <tr class="c-table__head">
                                    <th class="c-table__th">ステータス</th>
                                    <th class="c-table__th">内容</th>
                                    <th class="c-table__th">時刻</th>
                                    <th class="c-table__th">操作</th>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                            </table>
                        </div>

                        <div class="p-dashboard__panel" v-show="tab===5" key="5">
                            <table class="c-table">
                                <caption class="c-table__caption">○キーワードリスト</caption>
                                <tr class="c-table__head">
                                    <th class="c-table__th">ステータス</th>
                                    <th class="c-table__th">内容</th>
                                    <th class="c-table__th">時刻</th>
                                    <th class="c-table__th">操作</th>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                                <tr>
                                    <td class="c-table__td">[ツイート済]</td>
                                    <td class="c-table__td">
                                        吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                                    </td>
                                    <td class="c-table__td">2019年03月19日 21:51:13</td>
                                    <td class="c-table__td">編集/削除</td>
                                </tr>
                            </table>
                        </div>
                    </transition-group>
                </section>

            </div>
        </div>
    </main>
</template>

<script>
    import { OK } from '../utility'

    export default {
        data() {
            return {
                tab: 1,
                twitterUser: []
            }
        },
        methods: {
            async fetchTwitterUser() {
                const id = this.$store.state.auth.twitterId
                const response = await axios.get('/api/twitter/user/info/' + id)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.twitterUser = response.data
                // // console.log(users);
                // // console.log(accountNum);

            },
            async setTwitterId() {
                const response = await axios.post(`/api/twitter/${this.item.id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.$router.push('/dashboard')
            }
        },
        created() {
            this.fetchTwitterUser()
        }
    }
</script>