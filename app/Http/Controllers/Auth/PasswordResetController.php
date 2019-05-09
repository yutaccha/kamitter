<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordReset as MPassReset;
use App\PasswordReset;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param  [string] email
     * @return [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return response()->json([
                'errors' => [
                    'message' => "正しいメールアドレスを入力してください。"
                ]
            ], 422);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
            ]
        );
        if ($user && $passwordReset) {
            Mail::to($user)->send(new MPassReset($user, $passwordReset));
        }
        return response()->json([
            'message' => 'パスワードリセットのためのメールを送信しました。'
        ]);
    }

    /**
     * Reset password
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @param  [string] token
     * @return [string] message
     * @return [json] user object
     */
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string|confirmed',
            'token' => 'required|string'
        ]);
        $passwordReset = PasswordReset::where([
            ['token', $request->token],
            ['email', $request->email]
        ])->first();
        if (!$passwordReset)
            return response()->json([
                'errors' => [
                    'message' => 'フォームの有効期限が切れています。'
                ]
            ], 422);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'errors' => [
                    'message' => "正しいメールアドレスを入力してください。"
                ]
            ], 422);
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        return response()->json($user);
    }
}