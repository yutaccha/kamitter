//データの入れ物、中身を直接編集できない
const state = {
    //ユーザの認証状態を保存
    user: null,
    twitterId: null
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
    }
}

//stateを非同期処理で更新するメソッドAPIの通史語など
const actions = {
    async register(context, data) {
        const response = await axios.post('/api/register', data)
        //commitはミューテーションを呼び出すメソッド
        context.commit('setUser', response.data)
    },
    async login(context, data) {
        const response = await axios.post('/api/login', data)
        //commitはミューテーションを呼び出すメソッド
        context.commit('setUser', response.data)
    },
    async logout(context, data) {
        const response = await axios.post('/api/logout', data)
        context.commit('setUser', null)
    },
    async currentUser(context) {
        const response = await axios.get('/api/user')
        const user = response.data || null
        context.commit('setUser', user)
    },
    async currentTwitterUser(context) {
        const response = await axios.get('/api/twitter/id')
        const id = response.data || null
        context.commit('setTwitterUser', id)
    }
}

export default {
    namespaced: true,
    state,
    getters,
    mutations,
    actions,
}