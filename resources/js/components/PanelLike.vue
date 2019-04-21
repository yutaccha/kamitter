<template>
    <div class="p-dashboard__panel">
        <div class="c-status">
            <p class="c-status__show">稼働中</p>
            <button class="c-status__button c-button c-button--start">サービス開始</button>
            <button class="c-status__button c-button c-button--cancel">停止</button>
        </div>

        <table class="c-table">
            <caption class="c-table__caption">○いいね設定</caption>
            <tr class="c-table__head">
                <th class="c-table__th">条件</th>
                <th class="c-table__th">操作</th>
            </tr>
            <tr>
                <td class="c-table__td">プログラミング Boot</td>
                <td class="c-table__td">編集/削除</td>
            </tr>
        </table>
    </div>
</template>

<script>
    export default {
        name: "PanelLike"
    }
</script>