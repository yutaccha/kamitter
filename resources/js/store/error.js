/**
 * store内でAPIを実行する際に
 * エラーのレスポンコードを保存する
 */
const state = {
    code: null
}

const mutations = {
    setCode (state, code) {
        state.code = code
    }
}

export default {
    namespaced: true,
    state,
    mutations
}