<template>
    <div class="c-panel u-color__bg--white">

        <div class="p-status">
            <p class="p-status__show">{{serviceStatusLabel}}</p>
            <button class="p-status__button c-button c-button--success"
                    v-show="showRunButton"
                    @click.stop="runTweetService">サービス開始
            </button>
            <button class="p-status__button c-button c-button--danger"
                    v-show="showStopButton"
                    @click.stop="stopTweetService">停止
            </button>
        </div>


        <div class="p-table__title">
            <h2 class="p-table__caption">○自動ツイートリスト</h2>
            <button class="c-button c-button--twitter" @click="newModal = ! newModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                ツイートを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--tweet">ステータス</th>
                <th class="p-table__th p-table__th--tweet">ツイート内容</th>
                <th class="p-table__th p-table__th--tweet">時刻</th>
                <th class="p-table__th p-table__th--tweet">操作</th>
            </tr>

            <tr v-for="(autoTweet, index) in autoTweets" :key="autoTweet.id">
                <td class="p-table__td">{{autoTweet.status_label}}</td>
                <td class="p-table__td">{{autoTweet.tweet}}</td>
                <td class="p-table__td">{{autoTweet.japanese_formatted_date}}</td>
                <td class="p-table__td">
                    <template v-if="autoTweet.status === 1">
                        <button class="c-button c-button--twitter p-table__button"
                                @click.stop="showEditModal(autoTweet, index)"
                        >編集
                        </button>
                        <button class="c-button c-button--danger p-table__button"
                                @click.stop="removeAutoTweet(autoTweet.id, index)"
                        >削除
                        </button>
                    </template>
                </td>
            </tr>
        </table>


        <div class="p-modal__wrapper">
            <section class="p-modal p-modal--opened" v-show="newModal">
                <div class="p-modal__contents">
                            <span class="p-modal__cancel u-color__bg--white" @click="newModal = ! newModal">
                                <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                            </span>
                    <form class="p-form" @submit.prevent="addAutoTweet">

                        <div v-if="addErrors" class="p-form__errors">
                            <ul v-if="addErrors.date_time">
                                <li v-for="msg in addErrors.date_time" :key="msg">{{ msg }}</li>
                            </ul>
                        </div>

                        <label class="p-form__label" for="add-tweet">ツイート内容 {{addTextCount}}/140 *必須</label>
                        <textarea class="p-form__item p-form__item--textarea" id="add-tweet"
                                  rows="5" cols="40" v-model="addForm.tweet" required maxlength="140">
                        </textarea>

                        <label class="p-form__label">ツイート予定日時 *必須</label>
                        <div class="u-display__flex--left">
                            <input type="date" class="p-form__date"
                                   :min="getCurrentYYYYMMDD"
                                   v-model="addForm.date" required>
                            <input type="time" class="p-form__date"
                                   v-model="addForm.time" required>
                        </div>
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--twitter">追加</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal p-modal--opened" v-show="editModal">
                <div class="p-modal__contents">
                            <span class="p-modal__cancel u-color__bg--white" @click="editModal = ! editModal">
                                <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                            </span>
                    <form class="p-form" @submit.prevent="editAutoTweet">

                        <div v-if="editErrors" class="p-form__errors">
                            <ul v-if="editErrors.date_time">
                                <li v-for="msg in editErrors.date_time" :key="msg">{{ msg }}</li>
                            </ul>
                        </div>

                        <label class="p-form__label" for="edit-tweet">ツイート内容 {{editTextCount}}/140 *必須</label>
                        <textarea class="p-form__item p-form__item--textarea" id="edit-tweet"
                                  rows="5" cols="40" v-model="editForm.tweet" required maxlength="140">
                        </textarea>

                        <label class="p-form__label">予定日時 *必須</label>
                        <div class="u-display__flex--left">
                            <input type="date" class="p-form__date"
                                   :min="getCurrentYYYYMMDD"
                                   value="getCurrentYYYYMMDD"
                                   v-model="editForm.date" required>
                            <input type="time" class="p-form__date"
                                   v-model="editForm.time" required>
                        </div>
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--twitter">変更</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

    </div>
</template>

<script>
    import {CREATED, OK, UNPROCESSABLE_ENTRY} from "../utility";

    export default {
        data() {
            return {
                autoTweets: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                addErrors: null,
                editErrors: null,
                addForm: {
                    tweet: '',
                    date: '',
                    time: '00:00',
                },
                editForm: {
                    tweet: '',
                    date: '',
                    time: '',
                },
            }
        },
        computed: {
            /**
             * 新規作成フォームでTWEETの文字数をカウントする
             */
            addTextCount: function () {
                return this.addForm.tweet.length
            },
            /**
             * 編集フォームでTWEETの文字数をカウントする
             */
            editTextCount: function () {
                return this.editForm.tweet.length
            },
            /**
             * 現在の日付をYYYY-MM-DD形式で取得する
             */
            getCurrentYYYYMMDD: function () {
                const date = new Date()
                const year = date.getFullYear()
                const month = ("00" + (date.getMonth() + 1)).slice(-2)
                const day = ("00" + date.getDate()).slice(-2)
                return [year, month, day].join("-")
            },
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            },
        },
        methods: {
            /**
             * APIを使用して登録した自動ツイート一覧を取得する
             */
            async fetchAutoTweets() {
                const response = await axios.get('/api/tweet')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.autoTweets = response.data
            },
            /**
             * APIを使用して自動ツイートを新規登録する
             */
            async addAutoTweet() {
                this.clearErrors()
                const response = await axios.post('/api/tweet', this.addForm)

                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.addErrors = response.data.errors
                    return false
                }
                this.resetAddForm()
                if (response.status !== CREATED) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                const addTweet = response.data;
                this.autoTweets.push(addTweet)
                this.newModal = false
            },

            /**
             * APIを使用して自動ツイートを編集する
             */
            async editAutoTweet() {
                this.clearErrors()
                const response = await axios.put(`/api/tweet/${this.editForm.id}`, this.editForm)

                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.editErrors = response.data.errors
                    return false
                }
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.autoTweets.splice(this.editIndex, 1, response.data)
                this.resetEditForm()
            },

            /**
             * 自動ツイート編集用のモーダルフォームを表示する
             * 表示した際に、自動ツイートのデータをフォームに入力しておく
             */
            showEditModal(autoTweet, index) {
                this.editModal = true
                this.editForm.id = autoTweet.id
                this.editForm.tweet = autoTweet.tweet
                this.editForm.date = this.getYYYYMMDD(autoTweet.formatted_date)
                this.editForm.time = this.getHHMM(autoTweet.formatted_date)
                this.editIndex = index
            },

            /**
             * APIを使用して自動ツイートを削除する
             */
            async removeAutoTweet(id, index) {
                const response = await axios.delete(`/api/tweet/${id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.autoTweets.splice(index, 1)
            },
            /**
             * Datetime型をYYYY-MM-DD形式に変換する
             * @param formatted_date
             * @returns {string}
             */
            getYYYYMMDD(formatted_date) {
                const date = new Date(formatted_date)
                const year = date.getFullYear()
                const month = ("00" + (date.getMonth() + 1)).slice(-2)
                const day = ("00" + date.getDate()).slice(-2)
                return [year, month, day].join("-")
            },

            /**
             * Datetime型をHH:MMの形式に変換する
             */
            getHHMM(formatted_date) {
                const date = new Date(formatted_date)
                const hours = ("00" + date.getHours()).slice(-2)
                const minutes = ("00" + date.getMinutes()).slice(-2)
                return [hours, minutes].join(":")
            },
            /**
             * 新規登録フォームのリセットを行う
             */
            resetAddForm() {
                this.addForm.tweet = ''
                this.addForm.date = ''
                this.addForm.time = '00:00'
            },
            /**
             * 編集フォームのリセットを行う
             */
            resetEditForm() {
                this.editModal = false
                this.editForm.id = null
                this.editForm.tweet = ''
                this.editForm.date = ''
                this.editForm.time = ''
                this.editIndex = null
            },

            /**
             * APIを使用して自動ツイートのサービスステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get('/api/system/status')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_tweet_status
                this.serviceStatusLabel = response.data.status_labels.auto_tweet
            },

            /**
             * 自動ツイートサービスを稼働状態に変更する
             */
            async runTweetService() {
                const serviceType = 4
                const data = {type: serviceType}
                const response = await axios.post('/api/system/run', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_tweet_status
                this.serviceStatusLabel = response.data.status_labels.auto_tweet
            },

            /**
             * 自動ツイートサービスを停止状態にする
             */
            async stopTweetService() {
                const serviceType = 4
                const data = {type: serviceType}
                const response = await axios.post('/api/system/stop', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_tweet_status
                this.serviceStatusLabel = response.data.status_labels.auto_tweet
            },
            /**
             * フォームのエラーメッセージをクリアする
             */
            clearErrors() {
                this.addErrors = null
                this.editErrors = null
            }
        },
        created() {
            this.fetchAutoTweets()
            this.fetchServiceStatus()
        },
    }

</script>