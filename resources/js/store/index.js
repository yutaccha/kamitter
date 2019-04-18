import Vue from 'vue';
import Vuex from 'vuex'

import auth from './auth'
import error from './error'

//vueでvuexを使うための宣言
Vue.use(Vuex)

//ここでstoreインスタンスを定義
const store = new Vuex.Store({
    modules: {
        auth,
        error,
    }
})

export default store
