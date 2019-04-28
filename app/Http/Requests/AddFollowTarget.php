<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\FollowTarget;

class AddFollowTarget extends FormRequest
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
        return [
            'target' => "required|max:15|regex:/^[a-zA-Z0-9_]+$/i",
            'filter_word_id' => 'required',
        ];
    }
}
