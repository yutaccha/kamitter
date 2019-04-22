<template>
    <div class="c-panel u-color__bg--white">
        <div class="p-status">
            <p class="p-status__show">稼働中</p>
            <button class="p-status__button c-button c-button--success">サービス開始</button>
            <button class="p-status__button c-button c-button--danger">停止</button>
        </div>


        <div class="p-table__title">
            <h2 class="p-table__caption">○フォロワーターゲット</h2>
            <button class="c-button c-button--twitter" @click="viewModal = ! viewModal">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                フォロワーターゲットを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th">ステータス</th>
                <th class="p-table__th">ターゲット</th>
                <th class="p-table__th">条件</th>
                <th class="p-table__th">操作</th>
            </tr>
            <tr>
                <td class="p-table__td">[実行中]</td>
                <td class="p-table__td">@sample1234</td>
                <td class="p-table__td">プログラミング Boot</td>
                <td class="p-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="p-table__td">@fn103fdsafdasfasdfa1</td>
                <td class="p-table__td">プログラミング OR アーキテクチャ</td>
                <td class="p-table__td">[待機中]</td>
                <td class="p-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="p-table__td">@fteeewitn</td>
                <td class="p-table__td">フロントエンジニア 単価 -SES</td>
                <td class="p-table__td">[待機中]</td>
                <td class="p-table__td">編集/削除</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: "PanelFollow"
    }
</script>
