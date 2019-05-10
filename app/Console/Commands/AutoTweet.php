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
use Illuminate\Support\Facades\Log;

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
     * 設定されている自動ツイートを全て取得する
     * 取得した自動ツイートの、ツイート予定時間がYYYY-MM-DD HH:MMの形式で一致した場合
     * 現在のツイートだと認識して自動ツイートを行う
     * @return mixed
     */
    public function handle()
    {

        Log::info('=====================================================================');
        Log::info('AutoTweet : 開始');
        Log::info('=====================================================================');

        //auto_tweet_statusが稼動中のステータスになっているsystem_managersテーブルのレコードを取得する
        $auto_tweet_running_status_list = SystemManager::where('auto_tweet_status', SystemManager::STATUS_RUNNING)->get();


        foreach ($auto_tweet_running_status_list as $auto_tweet_running_status_item) {
            $system_manager_id = $auto_tweet_running_status_item->id;
            $twitter_user_id =  $auto_tweet_running_status_item->twitter_user_id;
            Log::info('#system_manager_id : ', [$system_manager_id]);
            Log::info('#twitter_user_id : ' , [$twitter_user_id]);

            //ユーザーごとの自動ツイート配列を取得する
            $auto_tweets_list = AutomaticTweet::where('twitter_user_id', $twitter_user_id)
                ->where('status', 1) //未ツイート
                ->with('twitterUser')->get();


            foreach ($auto_tweets_list as $auto_tweet) {
                Log::info('##自動ツイート開始');
                //投稿予定時刻なら自動ツイート
                if ($this->checkSubmitDateIsNowDate($auto_tweet)) {
                    //API実行
                    $api_result = $this->fetchTweetApi($auto_tweet);
                    //APIエラーの場合の処理と判定
                    $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
                    if ($flg_skip_to_next_user === true) {
                        break;
                    }
                    //ツイート完了のメールを送信
                    Log::info('##自動ツイート完了メール送信');
                    $this->sendMail($system_manager_id, $twitter_user_id, $auto_tweet);
                    $this->changeStatusTweeted($auto_tweet);
                    Log::info('##自動ツイート完了');

                }
            }

        }

        Log::info('=====================================================================');
        Log::info('AutoTweet : 終了');
        Log::info('=====================================================================');
    }

    /**
     * 自動ツイート完了のメールを送信する
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param $auto_tweet
     */
    private function sendMail($system_manager_id, $twitter_user_id, $auto_tweet)
    {
        $system_manager = SystemManager::find($system_manager_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $system_manager->user;
        Mail::to($user)->send(new CompleteTweet($user, $twitter_user, $auto_tweet));
    }

    /**
     * automatic_tweetのステータスをツイート済みに変更する
     * @param $auto_tweet
     */
    private function changeStatusTweeted($auto_tweet)
    {
        $auto_tweet->status = 2; //ツイート済み
        $auto_tweet->save();
    }


    /**
     * APIを使用して自動ツイートを行う
     * @param $auto_tweet
     * @return array|object
     */
    private function fetchTweetApi($auto_tweet)
    {
        Log::info('###API 自動ツイート開始');
        Log::debug('##ツイート内容: ', [$auto_tweet->tweet]);
        //APIに必要な変数の用意
        $token = $auto_tweet->twitterUser->token;
        $token_secret = $auto_tweet->twitterUser->token_secret;
        $param = [
            'status' => $auto_tweet->tweet,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_TWEET,
            $param, $token, $token_secret);

        Log::info('###API 自動ツイート完了');
        return $response_json;
    }


    /**
     * 現在時間と、ツイート予定時間を分レベルで比較して、同時刻の場合はtrueを返す
     * @param $auto_tweet
     * @return bool
     */
    private function checkSubmitDateIsNowDate($auto_tweet)
    {
        $submit_date = Carbon::create($auto_tweet->submit_date)->format('Y-m-d H:i');
        $now_date = Carbon::now()->format('Y-m-d H:i');
        Log::debug('###現在時間: ', [$now_date]);
        Log::debug('###ツイート予定時間: ', [$submit_date]);

        if ($submit_date === $now_date) {
            return true;
        }
        return false;
    }

}
