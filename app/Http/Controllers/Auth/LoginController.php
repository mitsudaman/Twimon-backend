<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\User;
use DB;

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

    public function redirectToProvider(Request $request)
    {
        $value = $request->cookie('laravel_session');
        error_log("-----------------login----------------------");
        error_log($value);

        $url = Socialite::driver('twitter')->redirect()->getTargetUrl();
        $temp = $request->session()->get('oauth.temp');
        error_log("-----------------oauth.temp----------------------");
        error_log(print_r($temp, true));

        return $url;
    }

    public function handleProviderCallback(Request $request)
    {
        // $value = $request->cookie('laravel_session');
        $hasNecessaryVerifier = $request->has('oauth_token') && $request->has('oauth_verifier');
        error_log("-----------------hasNecessaryVerifier----------------------");
        error_log($hasNecessaryVerifier);

        $temp = $request->session()->get('oauth.temp');
        error_log("-----------------temp----------------------");
        error_log(print_r($temp, true));
        
        $twitterUser = Socialite::driver('twitter')->user();
        $user = User::firstOrCreate([
            'account_id' => $twitterUser->getId(),
        ],[
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'name' => $twitterUser->getName(),
            'nickname' => $twitterUser->getNickname(),
            'twitter_token' => $twitterUser->token,
            'twitter_token_secret' => $twitterUser->tokenSecret,
            'sns_img_url' => str_replace_last('_normal', '', $twitterUser->getAvatar()),
            'twitter_followers_count' => $twitterUser->user['followers_count'],
            'description1' => '？？？？？？？？？？？？？？？？？？？？？',
            'description2' => '？？？？？？？？？？？？？？？？？？？？？',
            'description3' => '？？？？？？？？？？？？？？？？？？？？？',
        ]);
        $user->ip_address = $_SERVER['REMOTE_ADDR'];
        $user->save();

        if($user->wasRecentlyCreated){
            $items = [
                ['sentence1'=> 'こんにちわ'.$twitterUser->getName().'です。',
                'sentence2'=> $user->serial_number.'番目のツイモンとして誕生しました。',
                'sentence3'=> '私の戦闘力は'.$twitterUser->user['followers_count'].'です。',]
            ];
              
            $user->talks()->createMany($items);
        }
        return [
            'access_token' => $user->createToken('twimonToken')->accessToken,
            'me' => $user
        ];
    }
}
