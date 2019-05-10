<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;

/**
 * パスワードリセット申請時に送信するメールのクラス
 * Class PasswordReset
 * @package App\Mail
 */
class PasswordReset extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $password_reset;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, \App\PasswordReset $password_reset)
    {
        $this->user = $user;
        $this->password_reset = $password_reset;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('パスワード再設定のリンク')
            ->view('emails.passwordReset');
    }
}
