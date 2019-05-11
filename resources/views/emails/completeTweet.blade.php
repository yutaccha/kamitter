<p>
    {{ $user->name }}様、<br>
    いつもご利用いただきありがとうございます。
</p>

<p>
    Twitterアカウント :
    {{ $twitter_user->screen }}<br>
    にて自動ツイートが完了しました。
</p>

<p>
    どうぞよろしくお願い致します。
</p>

<p>「ツイート内容」<br>
    ======================================<br>
    {{ \App\Helpers\Helper::unEscapedLine($automatic_tweet->tweet) }}<br>
    ======================================
</p>

<p>
    Twitter自動集客ツール「{{ config('app.name') }}」
</p>