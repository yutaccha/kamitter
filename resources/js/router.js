import Vue from 'vue'
import VueRouter from 'vue-router'
// ページコンポーネントをインポートする
import Twitter from './pages/Twitter.vue'
import Login from './pages/Login.vue'

import store from './store'

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

const routes = [
    {
        path: '/login',
        component: Login,
        beforeEnter(to, from, next) {
            if(store.getters['auth/check']) {
                next('/twitter')
            }else{
                next()
            }
        }
    },
    {
        path: '/twitter',
        component: Twitter
    }
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: 'history',
    routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router