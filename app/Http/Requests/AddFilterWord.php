<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\FilterWord;

class AddFilterWord extends FormRequest
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
        $type_rule = Rule::in(array_keys(FilterWord::TYPE));

        return [

            'type' => 'required|' . $type_rule,  // 'type' => 'required|in(1, 2, 3)', となる
            'and' => 'max:100',
            'or' => 'max:100',
            'not' => 'max:100',
        ];
    }
}
