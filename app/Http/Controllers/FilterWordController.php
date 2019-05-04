<?php

namespace App\Http\Controllers;

use App\FilterWord;
use App\Http\Requests\AddFilterWord;
use Illuminate\Support\Facades\Auth;

class FilterWordController extends Controller
{
    public function add(AddFilterWord $request)
    {
        $filter = new FilterWord();

        $filter->type = $request->type;
        $filter->word = $this->adjustWordStyle($request->word);
        $filter->remove = $this->adjustWordStyle($request->remove);


        Auth::user()->filterWords()->save($filter);
        $new_filter = FilterWord::where('id', $filter->id)->with('user')->first();

        return response($new_filter, 201);
    }

    private function adjustWordStyle($preAdjustWord){
        $adjustedWord = mb_convert_kana($preAdjustWord, 's', 'UTF-8');
        $adjustedWord = preg_replace('/\s+/', ' ', $adjustedWord);
        if (' ' === mb_substr($adjustedWord, 0, 1))
        {
            $adjustedWord = mb_substr($adjustedWord, 1);
        }
        return $adjustedWord;
    }

    public function show()
    {
        $user_filter = Auth::user()->filterWords()->get();
        return response($user_filter, 200);
    }

    public function showOneFilter(int $id)
    {
        $user_filter = Auth::user()->filterWords()->where('id', $id)->first();
        return $user_filter ?? abort(404);
    }

    public function editFilter(int $id, AddFilterWord $request)
    {
        $user_filter = Auth::user()->filterWords()->where('id', $id)->first();
        if (! $user_filter){
            abort(404);
        }

        $user_filter->type = $request->type;
        $user_filter->word = $request->word;
        $user_filter->remove = $request->remove;
        $user_filter->save();

        return response($user_filter, 200);
    }

    public function deleteFilter(int $id){
        $user_filter = Auth::user()->filterWords()->where('id', $id)->first();
        if (! $user_filter){
            abort(404);
        }
        $user_filter->delete();
    }
}
