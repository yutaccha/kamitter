import Vue from 'vue'
import VueRouter from 'vue-router'
// ページコンポーネントをインポートする
import Twitter from './pages/Twitter.vue'
import Login from './pages/Login.vue'
import Dashboard from './pages/Dashboard.vue'
import S from './pages/System.vue'

import store from './store'
import System from "./pages/System";

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
                console.log('tw.dash');
                next('/dashboard')
            } else if (auth) {
                console.log('tw.tw');
                next()
            } else {
                next('login')
            }
        }
    },
    {
        path: '/dashboard',
        component: Dashboard,
        beforeEnter(to, from, next) {
            const auth = store.getters['auth/check']
            const twitterAuth = store.getters['auth/checkTwitterId']
            console.log(twitterAuth);
            if (auth && twitterAuth) {
                console.log('router.dash');
                next()
            } else if (auth) {
                console.log('router.twitter');
                next('/twitter')
            } else {
                next('/login')
            }
        }

    },
    {
        path: '/',
        beforeEnter(to, from, next) {
            const auth = store.getters['auth/check']
            if (auth && store.getters['auth/checkTwitterId']) {
                console.log('/.dash');
                next('/dashboard')
            } else if (auth) {
                console.log('/.twi');
                next('/twitter')
            } else {
                next('login')
            }
        }
    },
    {
        path: '/500',
        component: System,
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