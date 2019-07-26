<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Socialite;
// use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Twitterの認証ページヘユーザーをリダイレクト
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(Request $request)
    {
        // $url = Socialite::driver('twitter')->redirect()->getTargetUrl();
        $url = Socialite::with('twitter')->stateless()->redirect()->getTargetUrl();
               
        // $url = Socialite::driver('twitter')->stateless()->redirect()->getTargetUrl();
        error_log("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        error_log(print_r($request->session()->all(), true));
        // \Log::info("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        return $url;
    }
    /**
     * Twitterからユーザー情報を取得
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        // Socialite::driver('twitter')->redirect()->getTargetUrl();
        // error_log("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        // error_log(Socialite::driver('twitter')->redirect()->getTargetUrl());
        // error_log("aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa");
        // $user = Socialite::driver('twitter')->stateless()->user();
        // $user = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        // error_log($user);

        error_log("11111111111111111111111111111111111111111111111111");
        error_log(print_r($request->session()->all(), true));
        error_log("22222222222222222222222222222222222222222222222222");
        // return $user;
        // return Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        return Socialite::driver('twitter')->user();
        // return Socialite::driver('twitter')->user();
    }
}
