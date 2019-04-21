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
        $filter->and = $request->and;
        $filter->or = $request->or;
        $filter->not = $request->not;


        Auth::user()->filterWords()->save($filter);
        $new_filter = FilterWord::where('id', $filter->id)->with('user')->first();
        return response($new_filter, 201);
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

    public function update(int $id, AddFilterWord $request)
    {
        $user_filter = Auth::user()->filterWords()->where('id', $id)->first();
        if (! $user_filter){
            abort(404);
        }
        $user_filter->type = $request->type;
        $user_filter->and = $request->and;
        $user_filter->or = $request->or;
        $user_filter->not = $request->not;
        $user_filter->save();

        return response($user_filter, 201);
    }
}
