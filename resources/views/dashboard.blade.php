<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
    <title>{{ config('app.name') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body class="l-body">

<header class="l-header">
    <nav class="p-navbar">
        <h1 class="p-navbar__title">
            <a href="#" class="p-navbar__title_link">神ったー</a>
        </h1>

        <a href="#" class="p-navbar__login">ログイン/新規登録</a>
    </nav>
</header>

<main class="l-main">
    <div class="l-contents">

        <div class="p-contents__area">

            <section class="p-profile">
                <figure>
                    <div src="" alt="" class="p-profile__img"></div>
                </figure>
                <div class="p-profile__info">
                    <p class="p-profile__name">あやとまるるるるるるるるるる</p>
                    <p class="p-profile__screen">@ayatomarururururu</p>
                    <p class="p-profile__follow">
                        フォロー: <span class="p-profile__number">2011</span>
                        フォロワー: <span class="p-profile__number">5890</span>
                    </p>
                </div>
            </section>

            <section class="c-tab">
                <ul class="c-tab__list">
                    <li class="c-tab__item c-tab__item--active">自動フォロー</li>
                    <li class="c-tab__item">自動アンフォロー</li>
                    <li class="c-tab__item">自動いいね</li>
                    <li class="c-tab__item">自動ツイート</li>
                    <li class="c-tab__item">キーワード登録</li>
                </ul>
            </section>

            <section class="p-dashboard">

                {{--自動フォロー--}}
                <div class="p-dashboard__panel">
                    <div class="c-status">
                        <p class="c-status__show">稼働中</p>
                        <button class="c-status__button c-button c-button--start">サービス開始</button>
                        <button class="c-status__button c-button c-button--cancel">停止</button>
                    </div>

                    <table class="c-table">
                        <caption class="c-table__caption">○フォロワーターゲット</caption>
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

                {{--自動フォロー--}}
                <div class="p-dashboard__panel">
                    <div class="c-status">
                        <p class="c-status__show">稼働中</p>
                        <button class="c-status__button c-button c-button--start">サービス開始</button>
                        <button class="c-status__button c-button c-button--cancel">停止</button>
                    </div>
                </div>

                {{--いいね--}}
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

                {{--自動ツイート--}}
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
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                        <tr>
                            <td class="c-table__td">[ツイート済]</td>
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                        <tr>
                            <td class="c-table__td">[ツイート済]</td>
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                    </table>
                </div>

                {{--自動ツイート--}}
                <div class="p-dashboard__panel">
                    <table class="c-table">
                        <caption class="c-table__caption">○キーワードリスト</caption>
                        <tr class="c-table__head">
                            <th class="c-table__th">ステータス</th>
                            <th class="c-table__th">内容</th>
                            <th class="c-table__th">時刻</th>
                            <th class="c-table__th">操作</th>
                        </tr>
                        <tr>
                            <td class="c-table__td">[ツイート済]</td>
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                        <tr>
                            <td class="c-table__td">[ツイート済]</td>
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                        <tr>
                            <td class="c-table__td">[ツイート済]</td>
                            <td class="c-table__td">吾輩は猫である。名前はまだ無い。どこで生れたかとんと見当がつかぬ。何でも薄暗いじめじめした所でニャーニャー泣いていた事だけは記憶している。吾輩はここで始めて人間というものを見た。しかもあとで聞くとそれ</td>
                            <td class="c-table__td">2019年03月19日 21:51:13</td>
                            <td class="c-table__td">編集/削除</td>
                        </tr>
                    </table>
                </div>

            </section>

        </div>
    </div>
</main>

<footer class="l-foot c-foot">
    <p class="c-foot__p">Copyright © 神ったー. All Right Reserved.</p>
</footer>
</body>

</html>