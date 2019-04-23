<template>
    <div class="c-panel u-color__bg--white">
        <div class="p-status">
            <p class="p-status__show">稼働中</p>
            <button class="p-status__button c-button c-button--success">サービス開始</button>
            <button class="p-status__button c-button c-button--danger">停止</button>
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
            <tr v-for="(autoTweet, index) in autoTweets">
                <td class="p-table__td">{{autoTweet.status_label}}</td>
                <td class="p-table__td">{{autoTweet.tweet}}</td>
                <td class="p-table__td">{{autoTweet.japanese_formatted_date}}</td>
                <td class="p-table__td">
                    <button class="c-button c-button--twitter"
                            @click.stop="showEditModal(autoTweet, index)"
                    >編集
                    </button>
                    <button class="c-button c-button--danger"
                            @click.stop="removeAutoTweet(autoTweet.id, index)"
                    >削除
                    </button>
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


                        <label class="p-form__label" for="addTweet">ツイート内容 {{addTextCount}}/140</label>
                        <textarea class="p-form__item p-form__item--textarea" id="addTweet"
                                  rows="5" cols="40" v-model="addForm.tweet" required maxlength="140">
                        </textarea>

                        <label class="p-form__label">予定日時</label>
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


                        <label class="p-form__label" for="editTweet">ツイート内容 {{addTextCount}}/140</label>
                        <textarea class="p-form__item p-form__item--textarea" id="editTweet"
                                  rows="5" cols="40" v-model="editForm.tweet" required maxlength="140">
                        </textarea>

                        <label class="p-form__label">予定日時</label>
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
                errors: null,
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
            addTextCount: function () {
                return this.addForm.tweet.length
            },
            getCurrentYYYYMMDD: function () {
                const date = new Date()
                const year = date.getFullYear()
                const month = ("00" + (date.getMonth() + 1)).slice(-2)
                const day = ("00" + date.getDate()).slice(-2)
                return [year, month, day].join("-")
            }
        },
        methods: {
            async fetchAutoTweets() {
                const response = await axios.get('/api/tweet')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.autoTweets = response.data

            },
            async addAutoTweet() {
                const response = await axios.post('/api/tweet', this.addForm)
                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.errors = response.data.errors
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
            async editAutoTweet() {
                const response = await axios.put(`/api/tweet/${this.editForm.id}`, this.editForm)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.autoTweets.splice(this.editIndex, 1, response.data)
                this.resetEditForm()
            },
            showEditModal(autoTweet, index) {
                this.editModal = true
                this.editForm.id = autoTweet.id
                this.editForm.tweet = autoTweet.tweet
                this.editForm.date = this.getYYYYMMDD(autoTweet.formatted_date)
                this.editForm.time = this.getHHMM(autoTweet.formatted_date)
                this.editIndex = index
            },
            async removeAutoTweet(id, index) {
                const response = await axios.delete(`/api/tweet/${id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.autoTweets.splice(index, 1)
            },
            getYYYYMMDD(formatted_date) {
                const date = new Date(formatted_date)
                const year = date.getFullYear()
                const month = ("00" + (date.getMonth() + 1)).slice(-2)
                const day = ("00" + date.getDate()).slice(-2)
                return [year, month, day].join("-")
            },
            getHHMM(formatted_date) {
                const date = new Date(formatted_date)
                const hours = ("00" + date.getHours()).slice(-2)
                const minutes = ("00" + date.getMinutes()).slice(-2)
                return [hours, minutes].join(":")
            },
            resetAddForm() {
                this.addForm.tweet = ''
                this.addForm.date = ''
                this.addForm.time = '00:00'
            },
            resetEditForm() {
                this.editModal = false
                this.editForm.id = null
                this.editForm.tweet = ''
                this.editForm.date = ''
                this.editForm.time = ''
                this.editIndex = null
            }
        },
        created() {
            this.fetchAutoTweets()
        }
    }

</script>