/**
 * クッキーの値を取得する
 * @param {String} searchKey 検索するキー
 * @returns {String} キーに対応する値
 */
export function getCookieValue (searchKey) {
    if (typeof searchKey === 'undefined') {
        return ''
    }

    let val = ''

    document.cookie.split(';').forEach(cookie => {
        const [key, value] = cookie.split('=')
        if (key === searchKey) {
            return val = value
        }
    })

    return val
}

//Vueでで判別するためのステータスコード
export const OK = 200
export const CREATED = 201
export const UNAUTHORISED = 401
export const NOT_FOUND = 404
export const EXPIRED = 419
export const UNPROCESSABLE_ENTRY = 422
export const INTERNAL_SERVER_ERROR = 500