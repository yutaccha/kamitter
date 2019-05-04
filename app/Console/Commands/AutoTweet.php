<?php

namespace App\Console\Commands;

use App\AutomaticTweet;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Mail\CompleteTweet;
use Illuminate\Support\Facades\Mail;
use App\TwitterUser;

class AutoTweet extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:tweet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AutomaticTweeting with Twitter API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    //API_URL
    const API_URL_TWEET = 'statuses/update';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        //稼動中のステータスになっているsystem_managersテーブルのレコードを取得する
        $auto_tweet_running_status_list = SystemManager::where('auto_tweet_status', SystemManager::STATUS_RUNNING)->get();


        foreach ($auto_tweet_running_status_list as $auto_tweet_running_status_item) {

            $system_manager_id = $auto_tweet_running_status_item->id;
            $twitter_user_id =  $auto_tweet_running_status_item->twitter_user_id;

            //ユーザーごとの自動ツイート配列を取得する
            $auto_tweets_list = AutomaticTweet::where('twitter_user_id', $twitter_user_id)
                ->where('status', 1) //未ツイート
                ->with('twitterUser')->get();


            foreach ($auto_tweets_list as $auto_tweet) {
                //投稿予定時刻なら自動ツイート
                if ($this->checkSubmitDateIsNowDate($auto_tweet)) {
                    //API実行
                    $api_result = $this->fetchTweetApi($auto_tweet);
                    //APIエラーの場合の処理と判定
                    $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
                    if ($flg_skip_to_next_user === true) {
                        break;
                    }
                    $this->sendMail($system_manager_id, $twitter_user_id, $auto_tweet);
                    $this->changeStatusTweeted($auto_tweet);
                }
            }

        }

    }

    private function sendMail($system_manager_id, $twitter_user_id, $auto_tweet)
    {
        $system_manager = SystemManager::find($system_manager_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $system_manager->user;
        Mail::to($user)->send(new CompleteTweet($user, $twitter_user, $auto_tweet));
    }

    private function changeStatusTweeted($auto_tweet)
    {
        $auto_tweet->status = 2; //ツイート済み
        $auto_tweet->save();
    }

    private function fetchTweetApi($auto_tweet)
    {
        //APIに必要な変数の用意
        $token = $auto_tweet->twitterUser->token;
        $token_secret = $auto_tweet->twitterUser->token_secret;
        $param = [
            'status' => $auto_tweet->tweet,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_TWEET,
            $param, $token, $token_secret);

        return $response_json;
    }


    private function checkSubmitDateIsNowDate($auto_tweet)
    {
        $submit_date = Carbon::create($auto_tweet->submit_date)->format('Y-m-d H:i');
        $now_date = Carbon::now()->format('Y-m-d H:i');
        if ($submit_date === $now_date) {
            return true;
        }
        return false;
    }

}
