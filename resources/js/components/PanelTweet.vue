<template>
    <div class="c-panel u-color__bg--white">
        <div class="p-status">
            <p class="p-status__show">稼働中</p>
            <button class="p-status__button c-button c-button--success">サービス開始</button>
            <button class="p-status__button c-button c-button--danger">停止</button>
        </div>


        <div class="p-table__title">
            <h2 class="p-table__caption">○自動ツイートリスト</h2>
            <button class="c-button c-button--twitter" @click="newModal = ! newModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                ツイートを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th">ステータス</th>
                <th class="p-table__th">内容</th>
                <th class="p-table__th">時刻</th>
                <th class="p-table__th">操作</th>
            </tr>
            <tr>
                <td class="p-table__td">[ツイート済]</td>
                <td class="p-table__td">
                    吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ
                </td>
                <td class="p-table__td">2019年03月19日 21:51:13</td>
                <td class="p-table__td">編集/削除</td>
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