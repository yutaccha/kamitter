<p>
    {{ $user->name }}様、<br>
    いつもご利用いただきありがとうございます。
</p>

<p>
    パスワード再設定の申請が行われました。<br>
    以下のリンクにアクセスして、パスワード再設定を行ってください。
</p>

<a href=" {{url("/password/?token={$password_reset->token}")}} ">
パスワード再設定はこちら。
</a>

<p>
    どうぞよろしくお願い致します。
</p>

<p>
    Twitter自動集客ツール「{{ config('app.name') }}」
</p>