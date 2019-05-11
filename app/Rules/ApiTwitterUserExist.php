<?php

namespace App\Rules;

use App\TwitterUser;
use Illuminate\Contracts\Validation\Rule;
use App\Http\Components\TwitterApi;
use Illuminate\Support\Arr;

class ApiTwitterUserExist implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        /**
         * 指定のscreen_nameのツイッターユーザーが存在しているかをチェック
         * 存在していればtrueを返す、存在しなければfalseを返す
         */
        $twitter_user_id = session()->get('twitter_id');
        $twitter_user = TwitterUser::where('id', $twitter_user_id)->with('systemManagers')->first();
        $system_manager_id = $twitter_user->system_manager['id'];
        if( is_null($twitter_user)){
            return false;
        }

        $api_result = TwitterApi::getUsersShow($twitter_user, $value);
        info('result', [$api_result]);
        $api_error_flg = TwitterApi::handleApiError($api_result, $system_manager_id, $twitter_user_id);
        if ($api_error_flg) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return '指定のTwitterユーザーは存在しません.';
    }
}
