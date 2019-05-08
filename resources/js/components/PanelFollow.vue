<template>
    <div class="c-panel u-color__bg--white">

        <div class="p-status">
            <p class="p-status__show">{{serviceStatusLabel}}</p>
            <button class="p-status__button c-button c-button--success"
                    v-show="showRunButton"
                    @click.stop="runFollowService">サービス開始
            </button>
            <button class="p-status__button c-button c-button--danger"
                    v-show="showStopButton"
                    @click.stop="stopFollowService">停止
            </button>
        </div>


        <div class="p-table__title">
            <h2 class="p-table__caption">○ターゲットアカウントリスト</h2>
            <button class="c-button c-button--twitter" @click="newModal = ! newModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                ターゲットを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--follow">ステータス</th>
                <th class="p-table__th p-table__th--follow">ターゲット</th>
                <th class="p-table__th p-table__th--follow">条件</th>
                <th class="p-table__th p-table__th--follow">操作</th>
            </tr>

            <tr v-for="(followTarget, index) in followTargets">
                <td class="p-table__td">{{followTarget.status_label}}</td>
                <td class="p-table__td">@{{followTarget.target}}</td>
                <td class="p-table__td">{{followTarget.filter_word.merged_word}}</td>
                <td class="p-table__td">
                    <button class="c-button c-button--twitter p-table__button"
                            @click.stop="showEditModal(followTarget, index)"
                    >編集
                    </button>
                    <button class="c-button c-button--danger p-table__button"
                            @click.stop="removeFollowTarget(followTarget.id, index)"
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
                    <form class="p-form" @submit.prevent="addFollowTarget">

                        <label class="p-form__label" for="add-target">ターゲット名 *必須</label>
                        <input type="text" class="p-form__item" id="add-target"
                               v-model="addForm.target" required maxlength="15" placeholder="例) kamitter_1234">

                        <label class="p-form__label" for="add-target_filter_id">フォロー条件の選択 *必須</label>
                        <select class="p-form__select" id="add-target_filter_id"
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
                    <form class="p-form" @submit.prevent="editFollowTarget">

                        <label class="p-form__label" for="edit-target">ターゲット名 *必須</label>
                        <input type="text" class="p-form__item" id="edit-target"
                               v-model="editForm.target" required maxlength="15" placeholder="例) kamitter_1234">

                        <label class="p-form__label" for="eidt-target_filter_id">キーワード条件の選択 *必須</label>
                        <select class="p-form__select" id="eidt-target_filter_id"
                                v-model="editForm.filter_word_id"
                                required
                        >
                            <option v-for="filter in filters" :value="filter.id">{{filter.merged_word}}</option>
                            <optgroup></optgroup>
                        </select>
                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--twitter">編集</button>
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
                followTargets: [],
                filters: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                errors: null,
                addForm: {
                    target: null,
                    filter_word_id: null,
                },
                editForm: {
                    id: null,
                    target: null,
                    filter_word_id: null,
                },
            }
        },
        computed: {
            dashChange() {
                return this.$store.state.dashboard.noticeToTweet
            },
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            }
        },
        methods: {
            async fetchFollowTargets() {
                const response = await axios.get('/api/follow')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.followTargets = response.data
            },
            async fetchFilters() {
                const response = await axios.get('/api/filter')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.filters = response.data
            },
            async addFollowTarget() {
                const response = await axios.post('/api/follow', this.addForm)
                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.errors = response.data.errors
                    return false
                }
                this.resetAddForm()
                if (response.status !== CREATED) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.followTargets.unshift(response.data)
                this.newModal = false
            },
            showEditModal(followTarget, index) {
                this.editModal = true
                this.editForm.id = followTarget.id
                this.editForm.target = followTarget.target
                this.editForm.filter_word_id = followTarget.filter_word_id
                this.editIndex = index
            },
            async editFollowTarget() {
                const response = await axios.put(`/api/follow/${this.editForm.id}`, this.editForm)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.followTargets.splice(this.editIndex, 1, response.data)
                this.resetEditForm()
            },
            async removeFollowTarget(id, index) {
                const response = await axios.delete(`/api/follow/${id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.followTargets.splice(index, 1)
            },
            resetAddForm() {
                this.addForm.target = null
                this.addForm.filter_word_id = null
            },
            resetEditForm() {
                this.editModal = null
                this.editForm.id = null
                this.editForm.target = null
                this.editForm.filter_word_id = null
                this.editIndex = null
            },
            async fetchServiceStatus() {
                const response = await axios.get('/api/system/status')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_follow_status
                this.serviceStatusLabel = response.data.status_labels.auto_follow
            },
            async runFollowService() {
                const serviceType = 1
                const data = {type: serviceType}
                const response = await axios.post('/api/system/run', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_follow_status
                this.serviceStatusLabel = response.data.status_labels.auto_follow

            },
            async stopFollowService() {
                const serviceType = 1
                const data = {type: serviceType}
                const response = await axios.post('/api/system/stop', data)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.serviceStatus = response.data.auto_follow_status
                this.serviceStatusLabel = response.data.status_labels.auto_follow
            }

        },
        created() {
            this.fetchFollowTargets()
            this.fetchFilters()
            this.fetchServiceStatus()
        },
        watch: {
            dashChange: {
                handler(val) {
                    if (val === true) {
                        this.fetchFollowTargets()
                        this.fetchFilters()
                        this.$store.commit('dashboard/setNoticeToTweet', null)
                    }
                }
            },
        },

    }
</script>
