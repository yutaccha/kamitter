<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use App\AutomaticTweet;
use Illuminate\Foundation\Http\FormRequest;

class AddAutomaticTweet extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $status_rule = Rule::in(array_keys(AutomaticTweet::STATUS));

        return [
            'status' => 'required|' . $status_rule,  // 'type' => 'required|in(1, 2)', となる
            'tweet' => 'required|max:140',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
            'date_time' => 'after:now'
        ];
    }

    public function all($keys = null)
    {
        $results = parent::all($keys);

        if($this->filled('date') && $this->filled('time')) {

            $results['date_time'] = $this->input('date') .' '. $this->input('time');

        }

        return $results;
    }
}
