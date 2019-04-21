<template>
    <div class="p-dashboard__panel">
        <div class="c-status">
            <p class="c-status__show">稼働中</p>
            <button class="c-status__button c-button c-button--start">サービス開始</button>
            <button class="c-status__button c-button c-button--cancel">停止</button>
        </div>

        <table class="c-table">
            <caption class="c-table__caption">○自動ツイートリスト</caption>
            <tr class="c-table__head">
                <th class="c-table__th">ステータス</th>
                <th class="c-table__th">内容</th>
                <th class="c-table__th">時刻</th>
                <th class="c-table__th">操作</th>
            </tr>
            <tr>
                <td class="c-table__td">[ツイート済]</td>
                <td class="c-table__td">
                    吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                </td>
                <td class="c-table__td">2019年03月19日 21:51:13</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="c-table__td">[ツイート済]</td>
                <td class="c-table__td">
                    吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                </td>
                <td class="c-table__td">2019年03月19日 21:51:13</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="c-table__td">[ツイート済]</td>
                <td class="c-table__td">
                    吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                </td>
                <td class="c-table__td">2019年03月19日 21:51:13</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: "PanelTweet"
    }
</script>

<style scoped>

</style>