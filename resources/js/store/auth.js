import {CREATED, OK, UNPROCESSABLE_ENTRY } from '../utility'

const state = {
    //ユーザの認証状態を保存
    user: null,
    twitterId: null,
    apiStatus: null,
    //store内でAPIを使用するのでここでエラーをキャッチする
    loginErrorMessages: null,
    registerErrorMessages: null,
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
    },

}

//stateを非同期処理で更新するメソッドAPIの通信など
const actions = {


    /**
     * APIを使って会員登録を行う
     */
    async register(context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/register', data)

        // API成功時
        if (response.status === CREATED) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTRY) {
            context.commit('setRegisterErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, {root: true})
        }
    },


    /**
     * APIを使ってログインを行う
     */
    async login(context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/login', data)

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTRY) {
            context.commit('setLoginErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, {root: true})
        }
    },


    /**
     * APIを使ってログアウトを行う
     */
    async logout(context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/logout')

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            context.commit('setTwitterUser', null)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    },

    /**
     * APIを使ってユーザーログインチェックする
     * sessionからユーザーIDを取得する
     */
    async currentUser(context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/user')
        const user = response.data || null

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', user)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    },


    /**
     * APIを使ってツイッターユーザーログインチェックする
     * sessionからTwitterUserIdを取得する
     */
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
        context.commit('error/setCode', response.status, {root: true})
    },


    /**
     * APIを使ってツイッターユーザーログアウトする
     */
    async twitterUserLogout(context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/twitter/logout')
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setTwitterUser', null)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}