const state = {
    isChange: null
}

const mutations = {
    setChange (state, isChange) {
        state.isChange = isChange
    }
}

export default {
    namespaced: true,
    state,
    mutations
}