const state = {
    noticeToLike: null,
    noticeToTweet: null,
}

const mutations = {
    setNoticeToLike (state, noticeToLike) {
        state.noticeToLike = noticeToLike
    },
    setNoticeToTweet (state, noticeToTweet){
        state.noticeToTweet= noticeToTweet
    }
}

export default {
    namespaced: true,
    state,
    mutations
}