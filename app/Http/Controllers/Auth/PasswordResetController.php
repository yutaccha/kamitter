<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\User;
use App\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordReset as MPassReset;

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
                'message' => "正しいメールアドレスを入力してください。"
            ], 422);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => str_random(60)
             ]
        );
        if ($user && $passwordReset){
            Mail::to($user)->send(new MPassReset($user, $passwordReset));
        }
        return response()->json([
            'message' => 'パスワードリセットのためのメールを送信しました。'
        ]);
    }
    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return [string] message
     * @return [json] passwordReset object
     */
    public function find($token)
    {
        $passwordReset = PasswordReset::where('token', $token)
            ->first();
        if (!$passwordReset)
            return response()->json([
                'message' => 'フォームの有効期限が切れています。'
            ], 422);
        //パスワードリセットの有効期限切れ
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(900)->isPast()) {
            $passwordReset->delete();

            return response()->json([
                'message' => 'フォームの有効期限が切れています。'
            ], 422);
        }
        return response()->json($passwordReset);
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
                'message' => 'フォームの有効期限が切れています。'
            ], 422);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return response()->json([
                'message' => "正しいメールアドレスを入力してください。"
            ], 422);
        $user->password = bcrypt($request->password);
        $user->save();
        $passwordReset->delete();
        return response()->json($user);
    }
}