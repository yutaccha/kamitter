<?php

namespace App\Console\Commands;

use App\FollowerTarget;
use App\FollowHistory;
use App\FollowTarget;
use App\Http\Components\TwitterApi;
use App\SystemManager;
use App\TwitterUser;
use App\UnfollowHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\CompleteFollow;
use Illuminate\Support\Facades\Log;

class AutoFollow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:follow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatic follow with Twitter API';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    //abraham用 API URL
    const API_URL_FOLLOWERS_LIST = 'followers/list';
    const API_URL_FOLLOW = 'friendships/create';

    //フォロー回数を決めるのに使用
    const INTERVAL_HOURS = 2;
    const API_PER_A_DAY = 24 / self::INTERVAL_HOURS;

    //フォロワー数に応じた一日のフォロー上限数
    const FOLLOW_RATE_PER_DAY = [
        "100" => 20,
        "500" => 25,
        "1000" => 30,
        "1500" => 35,
        "2000" => 40,
        "3000" => 50,
    ];
    const FOLLOW_RATE_MAX = 50;


    /**
     * Execute the console command.
     * フォロワーターゲットリストを作成して自動フォローを行う。
     * フォロー後にフォロワーターゲットリストからフォローヒストリーにデータをmoveする。
     * @return mixed
     */
    public function handle()
    {
        Log::info('=====================================================================');
        Log::info('AutoFollow : 開始');
        Log::info('=====================================================================');


        //auto_follow_statusが稼動中のステータスになっているレコードを取得する
        $auto_follow_running_status_list = SystemManager::where('auto_follow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_follow_running_status_list as $auto_follow_running_status_item) {
            $system_manager_id = $auto_follow_running_status_item->id;
            $twitter_user_id = $auto_follow_running_status_item->twitter_user_id;
            Log::info('#system_manager_id : ', [$system_manager_id]);
            Log::info('#twitter_user_id : ' , [$twitter_user_id]);

            /**
             * ターゲットリストの作成
             */
            //最後に作成されたフォロワーターゲットリストのcursorカラムを見て
            //フォロワーターゲットリストを作る必要があるかないかを判定する
            $follower_target_list_latest = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->latest()->first();
            $twitter_api_follower_cursor =
                (!empty($follower_target_list_latest)) ? $follower_target_list_latest->cursor : null;

            Log::info('#cursor : ', [$twitter_api_follower_cursor]);

            //フォロワーターゲットリストが未作成、作成途中の場合はリストを作成する
            if (is_null($twitter_api_follower_cursor) || $twitter_api_follower_cursor !== '0') {
                $this->makeFollowerTargetList($system_manager_id, $twitter_user_id, $twitter_api_follower_cursor);
            }

            /**
             * 自動フォロー
             */
            $follower_target_list = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser')->get();
            //フォロワーターゲットリストがない場合は次のユーザーへスキップ
            if (is_null($follower_target_list)) {
                Log:info('#フォロワーターゲットリストが0件');
                Log::info('#次のユーザーにスキップ');
                continue;
            }

            $this->autoFollow($system_manager_id, $twitter_user_id, $follower_target_list);
        }
        Log::info('=====================================================================');
        Log::info('AutoFollow : 終了');
        Log::info('=====================================================================');
    }


    /**
     * バッチ実行時のフォローレート上限回数まで、APIを使って自動フォローを行う。
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param $follower_target_list
     */
    private function autoFollow($system_manager_id, $twitter_user_id, $follower_target_list)
    {
        Log::info('##フォロー開始');
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();

        $follow_counter = 0;
        //フォローレートの決定
        $follow_limit = $this->getFollowLimit($system_manager_id, $twitter_user_id);
        Log::info('##follow_limit', [$follow_limit]);


        foreach ($follower_target_list as $follower_target_item) {
            //APIでフォローを行う、エラーを検知した場合フォローを中止
            $api_result = $this->fetchAutoFollow($twitter_user, $follower_target_item->twitter_id);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                Log::notice('##APIエラー発生のためフォロー中止');
                return;
            }

            $this->moveFollowTargetsToFollowHistories($twitter_user_id, $follower_target_item);

            $follow_counter++;
            //レート上限を超えたら終了
            if ($follow_counter >= $follow_limit) {
                Log::info('##レート上限です。');
                break;
            }
        }

        // 全てのフォロワーターゲットをフォローした時点で自動フォロー完了
        $target_quantity = FollowerTarget::where('twitter_user_id', $twitter_user_id)->count();
        if($target_quantity === 0){
            Log::info('##フォローワーターゲットのフォローが完了しました');
            $this->sendMail($system_manager_id, $twitter_user_id);
        }
        Log::info('##フォロー完了');
    }

    /**
     * 自動フォロー完了メールを送信する
     * @param $system_manager_id
     * @param $twitter_user_id
     */
    private function sendMail($system_manager_id, $twitter_user_id)
    {
        $system_manager = SystemManager::find($system_manager_id)->with('user')->first();
        $twitter_user = TwitterUser::find($twitter_user_id)->first();
        $user = $system_manager->user;
        Mail::to($user)->send(new CompleteFollow($user, $twitter_user));
    }


    /**
     * follow_targetsテーブルの1カラムをfollow_historiesテーブルにmoveする。
     * @param $twitter_user_id
     * @param $follower_target_item
     */
    private function moveFollowTargetsToFollowHistories($twitter_user_id, $follower_target_item)
    {
        $follow_history = new FollowHistory();
        $follow_history->twitter_user_id = $twitter_user_id;
        $follow_history->twitter_id = $follower_target_item->twitter_id;
        $follow_history->save();

        $follower_target_item->delete();
    }


    /**
     * APIを使ってフォローを行う
     * @param $twitter_user
     * @param $user_id
     * @return array|object
     */
    private function fetchAutoFollow($twitter_user, $user_id)
    {
        Log::info('###API フォロー');
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_FOLLOW,
            $param, $token, $token_secret);

        Log::info('###API フォロー完了');
        return $response_json;
    }


    /**
     * その時点でのフォロー上限回数を取得する
     * 1日の上限÷1日の実行回数
     * @param $system_manager_id
     * @param $twitter_user_id
     * @return int
     */
    public function getFollowLimit($system_manager_id, $twitter_user_id)
    {
        $followers = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);

        //該当するフォロワー数と対応したレートを返す
        foreach (self::FOLLOW_RATE_PER_DAY as $rate => $limit) {
            if ((int)$rate >= (int)$followers) {
                return (int)($limit / self::API_PER_A_DAY);
            }
        }

        //上限のレートを返す
        return (int)(self::FOLLOW_RATE_MAX / self::API_PER_A_DAY);
    }


    /**
     * twitterのフォロワー数を取得する
     * @param $system_manager_id
     * @param $twitter_user_id
     * @return int
     */
    private function getTwitterFollowerNum($system_manager_id, $twitter_user_id)
    {
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();
        $api_result = TwitterApi::fetchTwitterUserInfo($twitter_user);
        $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
        if ($flg_skip_to_next_user === true) {
            return 0;
        }

        return $api_result->followers_count;
    }


    /**
     * フォロワーターゲットリストを作成する
     * @param $system_manager_id
     * @param $twitter_user_id
     * @param $cursor
     */
    private function makeFollowerTargetList($system_manager_id, $twitter_user_id, $cursor)
    {
        Log::info('##フォロワーリスト作成');

        $waiting_status = 1;
        $under_creating_status = 2;
        $created_status = 3;

        //ターゲットアカウントリストを1件取得
        $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->whereIn('status', [1, 2])->with('filterWord')->first();
        if (empty($follow_target)) {
            return;
        }

        //リスト作成中のステータスに変更
        if ($follow_target->status === $waiting_status) {
            $follow_target->status = $under_creating_status;
            $follow_target->save();
        }

        $filter_word = $follow_target->filterWord;
        $target_screen = $follow_target->target;

        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();


        do {
            //APIでフォロワーのリストを取得
            $api_result = $this->fetchGetFollowerListApi($twitter_user, $target_screen, $cursor);
            //エラーがあれば検索終了
            Log::debug('api_result', [$api_result]);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //取得したフォロワーのリストから、フォロワーターゲットリストに追加
            $this->addToFollowerTargetList($api_result, $filter_word, $twitter_user_id);

            $cursor = $api_result->next_cursor_str;
            //APIのフォロワーリストで次ページがなければ終了
        } while ($cursor !== "0");

        $follow_target->status = $created_status;
        $follow_target->save();

        Log::info('##フォロワーリスト作成完了');
    }


    /**
     * APIを使用して指定のユーザーをフォローしているユーザー一覧を取得する
     * @param $twitter_user
     * @param $target_screen
     * @param $cursor
     * @return array|object
     */
    private function fetchGetFollowerListApi($twitter_user, $target_screen, $cursor)
    {
        Log::info('###API フォロワーリスト取得');

        //APIオプション:1~200 指定の数のフォローワー情報を取得
        $count = 200;

        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'screen_name' => $target_screen,
            'count' => $count,
            'include_entities' => false,
        ];
        if (!empty($cursor)) {
            $param['cursor'] = $cursor;
        }

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('GET', self::API_URL_FOLLOWERS_LIST,
            $param, $token, $token_secret);

        Log::info('###API フォロワーリスト取得完了');

        return $response_json;
    }


    /**
     * バリデーションを行ってからフォロワーターゲットリストを作成する
     * @param $api_result
     * @param $filter_word
     * @param $twitter_user_id
     */
    private function addToFollowerTargetList($api_result, $filter_word, $twitter_user_id)
    {
        Log::info('####フォロワーターゲットリスト作成開始');
        Log::debug('####filter_word: ', [$filter_word->merged_word]);
        foreach ($api_result->users as $user) {
            $description = $user->description;
            Log::debug('####description: ', [$description]);

            //日本語プロフィールかチェック
            if (!$this->isJapaneseProfile($description)) {
                Log::debug('####日本人バリデーション');
                continue;
            }
            //プロフィールが条件フィルターに該当するかチェック
            if (!$this->isMatchedFilterWord($description, $filter_word)) {
                Log::debug('####プロフィールバリデーション');
                continue;
            }
            //アンフォローリストにいないか
            if ($this->isInUnfollowHistories($user, $twitter_user_id)) {
                Log::debug('####アンフォローバリデーション');
                continue;
            }
            //フォロー済リストから30日以内にフォローしてないか
            if ($this->isFollowedWithin30Days($user, $twitter_user_id)) {
                Log::debug('####フォロー済バリデーション');
                continue;
            }

            //ターゲットリストに追加
            Log::debug('####フォロワーターゲットリストにユーザーを追加');
            $new_follower_target = new FollowerTarget();
            $new_follower_target->twitter_user_id = $twitter_user_id;
            $new_follower_target->twitter_id = $user->id_str;
            $new_follower_target->cursor = $api_result->next_cursor_str;
            $new_follower_target->save();
            Log::debug('####追加したユーザー: ', [$new_follower_target]);
        }
        Log::info('####フォロワーターゲットリスト作成完了');
    }


    /**
     * アンフォロー履歴に入っているユーザならtrueを返す
     * @param $user
     * @param $twitter_user_id
     * @return bool
     */
    private function isInUnfollowHistories($user, $twitter_user_id)
    {
        $unfollow_history = UnfollowHistory::where('twitter_user_id', $twitter_user_id)
            ->where('twitter_id', $user->id_str)->first();
        if (is_null($unfollow_history)){
            return false;
        }
        return true;
    }


    /**
     * 30に以内にフォローしたユーザならtrueを返す
     * @param $user
     * @param $twitter_user_id
     * @return bool
     */
    private function isFollowedWithin30Days($user, $twitter_user_id)
    {
        $before_30days = Carbon::now()->addDay(-30);
        $follow_history = FollowHistory::where('twitter_user_id', $twitter_user_id)
            ->where('twitter_id', $user->id_str)
            ->whereDate('created_at', '>', $before_30days)->first();

        if (is_null($follow_history)){
            return false;
        }
        return true;

    }


    /**
     * 日本人のプロフィールならtrueを返す
     * @param String $description
     * @return bool
     */
    private function isJapaneseProfile(String $description)
    {
        if (strlen($description) === mb_strlen($description, 'utf8')) {
            return false;
        }
        return true;
    }


    /**
     * 設定されたフィルター条件をクリアしていればtrueを返す
     * @param String $description
     * @param $filter_word
     * @return bool
     */
    private function isMatchedFilterWord(String $description, $filter_word)
    {

        //除外ワードが含まれていればfalseを返す
        $removes = $filter_word->remove;
        if ($this->isIncludeRemove($description, $removes)) {
            Log::debug('#####除外ワードが含まれています');
            return false;
        }

        $word = $filter_word->word;
        $type_and = 1;
        $type_or = 2;
        //AND検索かOR検索の条件にマッチしていればtrueを返す
        if ($filter_word->type === $type_and) {
            Log::debug('#####AND条件を満たしません');
            return $this->isMatchedAndFilter($description, $word);
        } elseif ($filter_word->type === $type_or) {
            Log::debug('#####OR条件を満たしません');
            return $this->isMatchedOrFilter($description, $word);
        }

        return false;
    }

    /**
     * 文字列内に除外ワードが含まれていればtrueを返す
     * @param $description
     * @param $removes
     * @return bool
     */
    private function isIncludeRemove($description, $removes)
    {
        if (empty($removes)) {
            return false;
        }
        $remove_list = explode(' ', $removes);
        foreach ($remove_list as $remove) {
            if (strpos($description, $remove) !== false) {
                return true;
            }
        }
        return false;
    }


    /**
     * 文字列内に全ての条件ワードが入っていればtrueを返す
     * @param $description
     * @param $word
     * @return bool
     */
    private function isMatchedAndFilter($description, $word)
    {
        $and_word_list = explode(' ', $word);
        foreach ($and_word_list as $and_word) {
            if (strpos($description, $and_word) === false) {
                return false;
            }
        }
        return true;
    }


    /**
     * 文字列内にいずれかの条件ワードが入っていればtrueを返す
     * @param $description
     * @param $word
     * @return bool
     */
    private function isMatchedOrFilter($description, $word)
    {
        $or_word_list = explode(' ', $word);
        foreach ($or_word_list as $or_word) {
            if (strpos($description, $or_word) !== false) {
                return true;
            }
        }
        return false;
    }


}
