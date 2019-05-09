<template>
    <li class="c-card p-twitter__card u-color__bg--white" @click="setTwitterId">
        <div class="p-twitter__profile">
            <figure>
                <img class="p-twitter__img" :src="thumbnail" alt="">
            </figure>
            <div class="p-twitter__ids">
                <p class="p-twitter__name">{{name}}</p>
                <p class="p-twitter__id">@{{screenName}}</p>

            </div>
        </div>
        <div class="p-twitter__action">
            <transition-group name="t-del" mode="out-in">
                <i class="c-icon--gray p-twitter__icon fas fa-trash-alt" @click.stop="del = 2" v-show="del===1"
                   key="box"></i>
                <button class="p-twitter_delete c-button c-button--danger" @click.stop="deleteTwitterUser"
                        v-show="del===2" key="del">削除
                </button>
            </transition-group>
        </div>
    </li>
</template>

<script>
    import {OK} from '../utility'

    export default {
        props: {
            item: {
                type: Object,
                required: true
            },
            index: {
                type: Number,
                required: true
            }
        },
        data() {
            return {
                del: 1,
                screenName: "",
                name: "",
                thumbnail: ''
            }
        },
        methods: {
            /**
             * TwitterUserのユーザーデータを1件取得する
             */
            async fetchTwitterUser() {
                const response = await axios.get('/api/twitter/user/info/' + this.item.id)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.screenName = response.data.screen_name
                this.name = response.data.name
                this.thumbnail = response.data.thumbnail
            },
            /**
             * 使用するTwitterユーザーが選択された際に、セッションとstoreにTwitterUserIdを保存する
             * その後ダッシュボードに遷移する
             */
            async setTwitterId() {
                const response = await axios.post(`/api/twitter/${this.item.id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                await this.$store.dispatch('auth/currentTwitterUser')
                if (this.apiStatus) {
                    this.$router.push('/')
                }

                this.$router.push('/dashboard')
            },
            /**
             * TwitterUserIdをstoreから削除する
             * TwitterUserをDBから削除するAPIを実行する
             * APIが正常に完了した場合、Twitterページemitを通知して、削除の描画を行う
             */
            async deleteTwitterUser() {
                await this.$store.dispatch('auth/twitterUserLogout')
                if (this.apiStatus) {
                    const response = await axios.delete(`/api/twitter/${this.item.id}`)
                    if (response.status !== OK) {
                        this.$store.commit('error/setCode', response.status)
                        return false
                    }
                }
                this.$emit('delUser', {
                    index: this.index,
                })
            }
        },
        computed: {
            //storeを使ってAPIを実行する際に、APIのステータスを取得する
            apiStatus() {
                return this.$store.state.auth.apiStatus
            },
        },
        created() {
            this.fetchTwitterUser()
        }
    }
</script>
