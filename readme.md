# TwitterAPIを使った自動運用ツール


このアプリケーションは、設定した条件に合わせて自動フォロー、自動いいね、自動ツイートを行うことが出来るWEBアプリケーションです。

[アプリケーションはこちら](https://shikapro.xsrv.jp/kamitter/public/)

## 出来る事

### ユーザー管理
- ユーザ登録
- ログイン
- パスワードリマインダー
- Twitterアカウントの登録
- Twitterアカウントの削除

### 自動運用設定
- 条件キーワードの設定
- フォロー条件の設定
- 自動ツイート予約
- 自動いいねの設定
- それぞれのサービスの開始、停止

### バッチ処理
- 自動フォロー
- 自動ツイート
- 自動アンフォロー
- 自動ツイート
- 自動いいね

### その他
- サービスエラー時のメール通知
- 自動サービス完了時のメール通知

## 使用技術

### 開発全般
nginx, mysql, composer, npm, webpack

### フロントエンド
sass, FLOCSS, Vue.js, Vuex, Vue-router

### ックエンド
Laravel

## システム概要
- フロントとバックはAPIを使ってjsonでやり取りしています。
- フロントはVue.jsを使ったSPAです。
- バックエンドはLaravelをAPIサーバとして使用しています。

Vue ---- API通信 ---- Laravel ---- API通信 ---- Twitter.com

WEB上の設定画面からは、自動運用に関する設定を行い。
設定されたデータに基づいて、cronを使ったバッチ処理で実際に自動ツイートなどのアクションを行っています。
