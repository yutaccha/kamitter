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


    const API_URL_FOLLOWERS_LIST = 'followers/list';
    const API_URL_FOLLOW = 'friendships/create';

    const API_REQUEST_RATE_PER_DAY = 600;
    const INTERVAL_HOURS = 2;
    const API_PER_A_DAY = 24 / self::INTERVAL_HOURS;

    const FOLLOW_RATE_PER_DAY = [
        "100" => 20,
        "500" => 25,
        "1000" => 40,
        "1500" => 55,
        "2000" => 80,
        "3000" => 120,
    ];
    const FOLLOW_RATE_MAX = 150;


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //runのレコードを取得する
        //稼動中のステータスになっているauto_follow_statusのレコードを取得する
        $auto_follow_running_status_list = SystemManager::where('auto_follow_status', SystemManager::STATUS_RUNNING)->get();

        foreach ($auto_follow_running_status_list as $auto_follow_running_status_item) {
            $system_manager_id = $auto_follow_running_status_item->id;
            $twitter_user_id = $auto_follow_running_status_item->twitter_user_id;


            //最後に作成されたフォロワーターゲットリストのカラムを見て
            //フォロワーターゲットリストを作る必要があるかないかを判定する
            $follower_target_list_latest = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->latest()->first();
            $twitter_api_follower_cursor =
                (!empty($follower_target_list_latest)) ? $follower_target_list_latest->cursor : null;

            //フォロワーターゲットリストが未作成、作成途中の場合はリストを作成する
            if (is_null($twitter_api_follower_cursor) || $twitter_api_follower_cursor !== '0') {
                $this->makeFollowerTargetList($system_manager_id, $twitter_user_id, $twitter_api_follower_cursor);
            }


            $follower_target_list = FollowerTarget::where('twitter_user_id', $twitter_user_id)
                ->with('twitterUser')->get();
            //フォロワーターゲットリストがない場合は次のユーザーへスキップ
            if (is_null($follower_target_list)) {
                continue;
            }

            $this->autoFollow($system_manager_id, $twitter_user_id, $follower_target_list);
        }
    }


    private function autoFollow($system_manager_id, $twitter_user_id, $follower_target_list)
    {
        //API認証用のツイッターユーザー情報を取得
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->first();

        //フォローレートの決定
        $follow_counter = 0;
        $follow_limit = $this->getFollowLimit($system_manager_id, $twitter_user_id);


        foreach ($follower_target_list as $follower_target_item) {
            //APIでフォローを行う、エラーを検知した場合フォローを中止
            $api_result = $this->fetchAutoFollow($twitter_user, $follower_target_item->twitter_id);
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            $this->moveFollowTargetsToFollowHistories($twitter_user_id, $follower_target_item);

            $follow_counter++;
            //レート上限を超えたら終了
            if ($follow_counter >= $follow_limit) {
                break;
            }
        }

    }

    private function moveFollowTargetsToFollowHistories($twitter_user_id, $follower_target_item)
    {
        $follow_history = new FollowHistory();
        $follow_history->twitter_user_id = $twitter_user_id;
        $follow_history->twitter_id = $follower_target_item->twitter_id;
        $follow_history->save();

        $follower_target_item->delete();
    }


    private function fetchAutoFollow($twitter_user, $user_id)
    {
        //APIに必要な変数の用意
        $token = $twitter_user->token;
        $token_secret = $twitter_user->token_secret;
        $param = [
            'user_id' => $user_id,
        ];

        //API呼び出し
        $response_json = TwitterApi::useTwitterApi('POST', self::API_URL_FOLLOW,
            $param, $token, $token_secret);

        return $response_json;
    }


    public function getFollowLimit($system_manager_id, $twitter_user_id)
    {
        $followers = $this->getTwitterFollowerNum($system_manager_id, $twitter_user_id);

        foreach (self::FOLLOW_RATE_PER_DAY as $rate => $limit) {
            if ((int)$rate >= (int)$followers) {
                return (int)($limit / self::API_PER_A_DAY);
            }
        }

        return (int)(self::FOLLOW_RATE_MAX / self::API_PER_A_DAY);
    }


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

    private function makeFollowerTargetList($system_manager_id, $twitter_user_id, $cursor)
    {
        $waiting_status = 1;
        $under_creating_status = 2;
        $created_status = 3;

        //ターゲットアカウントリストを取得
        $follow_target = FollowTarget::where('twitter_user_id', $twitter_user_id)
            ->whereIn('status', [1, 2])->with('filterWord')->first();
        if (empty($follow_target)) {
            return;
        }

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
            $flg_skip_to_next_user = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
            if ($flg_skip_to_next_user === true) {
                return;
            }

            //取得したフォロワーのリストから、フォローターゲットリストに追加
            $this->addToFollowTargetList($api_result, $filter_word, $twitter_user_id);

            $cursor = $api_result->next_cursor_str;
            //APIのフォロワーリストで次ページがなければ終了
        } while ($cursor !== "0");

        $follow_target->status = $created_status;
        $follow_target->save();
    }


    private function fetchGetFollowerListApi($twitter_user, $target_screen, $cursor)
    {
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

        return $response_json;
    }


    private function addToFollowTargetList($api_result, $filter_word, $twitter_user_id)
    {
        foreach ($api_result->users as $user) {
            $description = $user->description;

            //日本語プロフィールかチェック
            if (!$this->isJapaneseProfile($description)) {
                continue;
            }
            //プロフィールが条件フィルターに該当するかチェック
            if (!$this->isMatchedFilterWord($description, $filter_word)) {
                continue;
            }
            //アンフォローリストにいないか
            if ($this->isInUnfollowHistories($user, $twitter_user_id)) {
                info('isunfollowhist');
                info($user->id_str);
                continue;
            }
            //フォロー済リストから30日以内にフォローしてないか
            if ($this->isFollowedWithin30Days($user, $twitter_user_id)) {
                info('is30followhist');
                info($user->id_str);
                continue;
            }

            //ターゲットリストに追加
            $new_follower_target = new FollowerTarget();
            $new_follower_target->twitter_user_id = $twitter_user_id;
            $new_follower_target->twitter_id = $user->id_str;
            $new_follower_target->cursor = $api_result->next_cursor_str;
            $new_follower_target->save();
        }
    }

    private function isInUnfollowHistories($user, $twitter_user_id)
    {
        $unfollow_history = UnfollowHistory::where('twitter_user_id', $twitter_user_id)
            ->where('twitter_id', $user->id_str)->first();
        if (is_null($unfollow_history)){
            return false;
        }
        return true;
    }

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

    private function isJapaneseProfile(String $description)
    {
        if (strlen($description) === mb_strlen($description, 'utf8')) {
            return false;
        }
        return true;
    }


    private function isMatchedFilterWord(String $description, $filter_word)
    {

        //除外ワードが含まれていればfalseを返す
        $removes = $filter_word->remove;
        if ($this->isIncludeRemove($description, $removes)) {
            return false;
        }

        $word = $filter_word->word;
        $type_and = 1;
        $type_or = 2;
        //AND検索かOR検索の条件にマッチしていればtrueを返す
        if ($filter_word->type === $type_and) {
            return $this->isMatchedAndFilter($description, $word);
        } elseif ($filter_word->type === $type_or) {
            return $this->isMatchedOrFilter($description, $word);
        }

        return false;
    }


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
