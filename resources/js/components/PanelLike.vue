<template>
    <div class="c-panel u-color__bg--white">

        <div class="p-status">
            <p class="p-status__show">{{serviceStatusLabel}}</p>
            <button class="p-status__button c-button c-button--success"
                    v-show="showRunButton"
                    @click.stop="runLikeService">サービス開始
            </button>
            <button class="p-status__button c-button c-button--danger"
                    v-show="showStopButton"
                    @click.stop="stopLikeService">停止
            </button>
        </div>


        <div class="p-table__title">
            <h2 class="p-table__caption">○自動いいねリスト</h2>
            <button class="c-button c-button--twitter" @click="newModal = ! newModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                いいね設定を追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--like">いいね条件</th>
                <th class="p-table__th p-table__th--like">操作</th>
            </tr>

            <tr v-for="(like, index) in likes">
                <th class="p-table__td">{{like.filter_word.merged_word}}</th>
                <th class="p-table__td">
                    <button class="c-button c-button--twitter p-table__button"
                            @click.stop="showEditModal(like, index)"
                    >編集
                    </button>
                    <button class="c-button c-button--danger p-table__button"
                            @click.stop="removeLike(like.id, index)"
                    >削除
                    </button>
                </th>
            </tr>
        </table>

        <div class="p-modal__wrapper">
            <section class="p-modal p-modal--opened" v-show="newModal">
                <div class="p-modal__contents">
                            <span class="p-modal__cancel u-color__bg--white" @click="newModal = ! newModal">
                                <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                            </span>
                    <form class="p-form" @submit.prevent="addLike">


                        <label class="p-form__label" for="add-like-filter">いいね条件の選択</label>
                        <select class="p-form__select" id="add-like-filter"
                                v-model="addForm.filter_word_id"
                                required
                        >
                            <option v-for="filter in filters" :value="filter.id">{{filter.merged_word}}</option>
                            <optgroup></optgroup>
                        </select>
                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
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
                    <form class="p-form" @submit.prevent="editLike">


                        <label class="p-form__label" for="edit-like-filter">いいね条件の選択</label>
                        <select class="p-form__select" id="edit-like-filter"
                                v-model="editForm.filter_word_id"
                                required
                        >
                            <option v-for="filter in filters" :value="filter.id">{{filter.merged_word}}</option>
                            <optgroup></optgroup>
                        </select>
                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
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
    import {CREATED, OK, UNPROCESSABLE_ENTRY} from "../utility"

    export default {
        data() {
            return {
                likes: [],
                filters: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                errors: null,
                addForm: {
                    filter_word_id: null,
                },
                editForm: {
                    id: null,
                    filter_word_id: null,
                },
            }
        },
        computed: {
            dashChange() {
                return this.$store.state.dashboard.noticeToLike
            },
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            }
        },
        methods: {
            async fetchLikes() {
                const response = await axios.get('/api/like')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.likes = response.data
            },
            async fetchFilters() {
                const response = await axios.get('/api/filter')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.filters = response.data
            },
            async addLike() {
                const response = await axios.post('/api/like', this.addForm)
                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.errors = response.data.errors
                    return false
                }
                this.addForm.filter_word_id = null
                if (response.status !== CREATED) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.likes.push(response.data)
                this.newModal = false
            },
            showEditModal(like, index) {
                this.editModal = true
                this.editForm.id = like.id
                this.editForm.filter_word_id = like.filter_word_id
                this.editIndex = index
            },
            async editLike() {
                const response = await axios.put(`/api/like/${this.editForm.id}`, this.editForm)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.likes.splice(this.editIndex, 1, response.data)
                this.resetEditForm()
            },
            async removeLike(id, index) {
                const response = await axios.delete(`/api/like/${id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.likes.splice(index, 1)
            },
            resetEditForm() {
                this.editModal = false
                this.editForm.id = null
                this.editForm.filter_word_id = null
                this.editIndex = null
            },
            async fetchServiceStatus() {
                const response = await axios.get('/api/system/status')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.serviceStatus = response.data.auto_like_status
                this.serviceStatusLabel = response.data.status_labels.auto_like
            },
            async runLikeService() {
                const serviceType = 3
                const data = {type: serviceType}
                const response = await axios.post('/api/system/run', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_like_status
                this.serviceStatusLabel = response.data.status_labels.auto_like
            },
            async stopLikeService() {
                const serviceType = 3
                const data = {type: serviceType}
                const response = await axios.post('/api/system/stop', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_like_status
                this.serviceStatusLabel = response.data.status_labels.auto_like
            }

        },
        created() {
            this.fetchLikes()
            this.fetchFilters()
            this.fetchServiceStatus()
        },
        watch: {
            dashChange: {
                handler(val) {
                    if (val === true) {
                        this.fetchLikes()
                        this.fetchFilters()
                        this.$store.commit('dashboard/setNoticeToLike', null)
                    }
                }
            },
        }
    }
</script>