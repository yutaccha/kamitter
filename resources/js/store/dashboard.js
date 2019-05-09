/**
 *自動ツイート、自動いいね機能ではフィルターキーワードを参照しているので、
 * フィルターキーワードに変更があった際に変更を通知する
 * 通知を保存する
 */
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