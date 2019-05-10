<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\TwitterUser;
use App\AutomaticTweet;

/**
 * 自動ツイート完了時に送信するメールのクラス
 * Class CompleteTweet
 * @package App\Mail
 */
class CompleteTweet extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $twitter_user;
    public $automatic_tweet;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user, TwitterUser $twitter_user, AutomaticTweet $automatic_tweet)
    {
        $this->user = $user;
        $this->twitter_user = $twitter_user;
        $this->automatic_tweet = $automatic_tweet;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('自動ツイート完了のお知らせ')
            ->view('emails.completeTweet');
    }
}
