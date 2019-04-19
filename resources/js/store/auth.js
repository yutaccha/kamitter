import {CREATED, OK, UNPROCESSABLE_ENTRY } from '../utility'


//データの入れ物、中身を直接編集できない
const state = {
    //ユーザの認証状態を保存
    user: null,
    twitterId: null,
    apiStatus: null,
    loginErrorMessages: null,
    registerErrorMessages: null
}

//stateの算出プロパティ
const getters = {
    check: state => !!state.user,
    username: state => state.user ? state.user.name : '',
    checkTwitterId: state => !!state.twitterId,
}

//stateを同期処理で更新するメソッド
const mutations = {
    setUser(state, user) {
        state.user = user
    },
    setTwitterUser(state, id) {
        state.twitterId = id;
    },
    setApiStatus(state, status) {
        state.apiStatus = status
    },
    setLoginErrorMessages (state, messages) {
        state.loginErrorMessages = messages
    },
    setRegisterErrorMessages (state, messages) {
        state.registerErrorMessages = messages
    }
}

//stateを非同期処理で更新するメソッドAPIの通史語など
const actions = {
    // 会員登録
    async register (context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/register', data)

        if (response.status === CREATED) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTRY) {
            context.commit('setRegisterErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, { root: true })
        }
    },

    // ログイン
    async login (context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/login', data)
        console.log(response);

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTRY) {
            context.commit('setLoginErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, { root: true })
        }
    },

    // ログアウト
    async logout (context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/logout')

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, { root: true })
    },

    // ログインユーザーチェック
    async currentUser (context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/user')
        const user = response.data || null

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', user)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, { root: true })
    },

    async currentTwitterUser(context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/twitter/id')
        const id = response.data || null

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setTwitterUser', id)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, { root: true })
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}