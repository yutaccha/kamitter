<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\User;
use App\TwitterUser;

class ExceededLimit extends Mailable
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
            ->subject('API利用制限のお知らせ')
            ->view('emails.exceeded');
    }
}
