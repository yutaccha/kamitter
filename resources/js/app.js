import './bootstrap'
import Vue from 'vue'
import router from './router'
import store from './store'

import App from './App.vue'

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
