<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\TwitterUser;


/**
 * 自動フォロー完了時に送信するメールのクラス
 * Class CompleteFollow
 * @package App\Mail
 */
class CompleteFollow extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $twitter_user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, TwitterUser $twitter_user)
    {
        $this->user = $user;
        $this->twitter_user = $twitter_user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('自動フォロー完了のお知らせ')
            ->view('emails.completeFollow');
    }
}
