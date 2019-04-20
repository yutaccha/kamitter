import Vue from 'vue'
import VueRouter from 'vue-router'
// ページコンポーネントをインポートする
import Twitter from './pages/Twitter.vue'
import Login from './pages/Login.vue'
import Dashboard from './pages/Dashboard.vue'
import SystemError from './pages/System.vue'

import store from './store'

// VueRouterプラグインを使用する
// これによって<RouterView />コンポーネントなどを使うことができる
Vue.use(VueRouter)

const routes = [
    {
        path: '/login',
        component: Login,
        beforeEnter(to, from, next) {
            if (store.getters['auth/check']) {
                next('/twitter')
            } else {
                next()
            }
        }
    },
    {
        path: '/twitter',
        component: Twitter,
        beforeEnter(to, from, next) {
            const auth = store.getters['auth/check']
            if (auth && store.getters['auth/checkTwitterId']) {
                next('/dashboard')
            } else if (auth) {
                next()
            } else {
                next('login')
            }
        }
    },
    {
        path: '/dashboard',
        component: Dashboard,
    },
    {
        path: '/',
        beforeEnter(to, from, next) {
            const auth = store.getters['auth/check']
            if (auth && store.getters['auth/checkTwitterId']) {
                next('/dashboard')
            } else if (auth) {
                next('/twitter')
            } else {
                next('login')
            }
        }
    },
    {
      path: '/500',
      component: SystemError,
    },
]

// VueRouterインスタンスを作成する
const router = new VueRouter({
    mode: 'history',
    routes
})

// VueRouterインスタンスをエクスポートする
// app.jsでインポートするため
export default router