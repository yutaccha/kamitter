import Vue from 'vue';
import Vuex from 'vuex'

import auth from './auth'

//vueでvuexを使うための宣言
Vue.use(Vuex)

//ここでstoreインスタンスを定義
const store = new Vuex.Store({
    modules: {
        auth,
    }
})

export default store
