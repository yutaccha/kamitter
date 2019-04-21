<template>
    <div class="c-panel u-color__bg--white">
        <div class="c-status">
            <p class="c-status__show">稼働中</p>
            <button class="c-status__button c-button c-button--success">サービス開始</button>
            <button class="c-status__button c-button c-button--danger">停止</button>
        </div>


        <div class="c-table__title">
            <h2 class="c-table__caption">○フォロワーターゲット</h2>
            <button class="c-button c-button--twitter">
                <i class="c-icon c-icon--white fas fa-plus"></i>
                フォロワーターゲットを追加
            </button>
        </div>
        <table class="c-table">
            <tr class="c-table__head">
                <th class="c-table__th">ステータス</th>
                <th class="c-table__th">ターゲット</th>
                <th class="c-table__th">条件</th>
                <th class="c-table__th">操作</th>
            </tr>
            <tr>
                <td class="c-table__td">[実行中]</td>
                <td class="c-table__td">@sample1234</td>
                <td class="c-table__td">プログラミング Boot</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="c-table__td">@fn103fdsafdasfasdfa1</td>
                <td class="c-table__td">プログラミング OR アーキテクチャ</td>
                <td class="c-table__td">[待機中]</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
            <tr>
                <td class="c-table__td">@fteeewitn</td>
                <td class="c-table__td">フロントエンジニア 単価 -SES</td>
                <td class="c-table__td">[待機中]</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: "PanelFollow"
    }
</script>
