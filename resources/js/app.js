import './bootstrap'
import Vue from 'vue'
import router from './router'
import store from './store'

import App from './App.vue'

/**
 * 一番最初の初期描画の時に、ユーザ認証とTwitterUser認証を行う
 */
const createApp = async () => {
    await store.dispatch('auth/currentUser')
    await store.dispatch('auth/currentTwitterUser')

    new Vue({
        el: '#app',
        router,
        store,
        components: {App},
        template: '<App />'
    })
}

createApp()
