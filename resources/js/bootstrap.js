/**
 * LaravelにCSRFトークンをヘッダーでチェックさせる設定
 */
import { getCookieValue } from './utility'

window.axios = require('axios')

// Ajaxリクエストであることを示すヘッダーを追加する
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

window.axios.interceptors.request.use(config => {
    // クッキーからトークンを取り出してヘッダーに追加する
    config.headers['X-XSRF-TOKEN'] = getCookieValue('XSRF-TOKEN')

    return config
})

// window.axios.baseURL = process.env.MIX_BASE_URL


/**
 * エラーが帰ってきた場合は、エラーのレスポンスオブジェクトを取得する
 */
window.axios.interceptors.response.use(
    response => response,
    error => error.response || error
)