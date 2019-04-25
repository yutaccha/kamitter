<template>
    <div class="c-panel u-color__bg--white">
        <div class="p-table__title">
            <h2 class="p-table__caption">○キーワードリスト</h2>
            <button class="c-button c-button--twitter" @click="newModal = ! newModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                キーワードを追加
            </button>
        </div>


        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--filter">条件タイプ</th>
                <th class="p-table__th p-table__th--filter">キーワード</th>
                <th class="p-table__th p-table__th--filter">除外ワード</th>
                <th class="p-table__th p-table__th--filter">操作</th>
            </tr>
            <tr v-for="(filter, index) in filters">
                <td class="p-table__td">{{filter.type_label}}</td>
                <td class="p-table__td">{{filter.word}}</td>
                <td class="p-table__td">{{filter.remove}}</td>
                <td class="p-table__td">
                    <button class="c-button c-button--twitter"
                            @click.stop="showEditModal(filter, index)"
                    >編集
                    </button>
                    <button class="c-button c-button--danger"
                            @click.stop="removeFilter(filter.id, index)"
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
                    <form class="p-form" @submit.prevent="addFilter">


                        <label class="p-form__label" for="add-filter">条件タイプ</label>
                        <select class="p-form__select" id="add-filter" v-model="addForm.type">
                            <option value="1">次のワードを含む</option>
                            <option value="2">いずれかのワードを含む</option>
                        </select>
                        <label class="p-form__label" for="keyword">キーワード *必須</label>
                        <input type="text" class="p-form__item" id="keyword"
                               v-model="addForm.word" required maxlength="50">

                        <label class="p-form__label" for="remove_word">除外ワード</label>
                        <input type="text" class="p-form__item" id="remove_word"
                               v-model="addForm.remove" maxlength="50">
                        <p class="p-form__notion">※複数ワードを指定する際は、「ツイッター 神」のように半角スペースで区切ってください。</p>
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
                    <form class="p-form" @submit.prevent="editFilter">


                        <label class="p-form__label" for="edit-filter">条件タイプ</label>
                        <select class="p-form__select" id="edit-filter" v-model="editForm.type">
                            <option value="1">次のワードを含む</option>
                            <option value="2">いずれかのワードを含む</option>
                        </select>
                        <label class="p-form__label" for="edit-keyword">キーワード ※必須</label>
                        <input type="text" class="p-form__item" id="edit-keyword"
                               v-model="editForm.word" required maxlength="50">

                        <label class="p-form__label" for="edit-remove_keyword">除外ワード</label>
                        <input type="text" class="p-form__item" id="edit-remove_keyword"
                               v-model="editForm.remove" maxlength="50">
                        <p class="p-form__notion">※複数ワードを指定する際は、「ツイッター 神」のように半角スペースで区切ってください。</p>
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
                filters: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                errors: null,
                addForm: {
                    type: 1,
                    word: '',
                    remove: ''
                },
                editForm: {
                    id: null,
                    type: '',
                    word: '',
                    remove: '',
                },
            }
        },
        methods: {
            async fetchFilters() {
                const response = await axios.get('/api/filter')
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.filters = response.data
            },
            async addFilter() {
                const response = await axios.post('/api/filter', this.addForm)
                if (response.status === UNPROCESSABLE_ENTRY) {
                    this.errors = response.data.errors
                    return false
                }

                this.resetAddForm()

                if (response.status !== CREATED) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                const addedFilter = response.data;
                this.filters.push(addedFilter)
                this.newModal = false
                this.$store.commit('dashboard/setNoticeToTweet', true)
                this.$store.commit('dashboard/setNoticeToLike', true)
            },
            showEditModal(filter, index) {
                this.editModal = true
                this.editForm.id = filter.id
                this.editForm.type = filter.type
                this.editForm.word = filter.word
                this.editForm.remove = filter.remove
                this.editIndex = index
            },
            async editFilter() {
                const response = await axios.put(`/api/filter/${this.editForm.id}`, this.editForm)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }
                this.filters.splice(this.editIndex, 1, response.data)
                this.resetEditForm()
                this.$store.commit('dashboard/setNoticeToTweet', true)
                this.$store.commit('dashboard/setNoticeToLike', true)
            },
            async removeFilter(id, index) {
                const response = await axios.delete(`/api/filter/${id}`)
                if (response.status !== OK) {
                    this.$store.commit('error/setCode', response.status)
                    return false
                }

                this.filters.splice(index, 1)
                this.$store.commit('dashboard/setNoticeToTweet', true)
                this.$store.commit('dashboard/setNoticeToLike', true)
            },

            resetAddForm() {
                this.addForm.type = 1
                this.addForm.word = ''
                this.addForm.remove = ''
            },
            resetEditForm() {
                this.editModal = false
                this.editForm.id = null
                this.editForm.type = ''
                this.editForm.word = ''
                this.editForm.remove = ''
                this.editIndex = null
            }
        },
        created() {
            this.fetchFilters();
        },
    }
</script>