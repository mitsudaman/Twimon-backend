<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use App\User;
use DB;
use Illuminate\Http\JsonResponse;

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
        $url = Socialite::driver('twitter')->redirect()->getTargetUrl();
        return [
            'redirect_url' => $url,
        ];
    }

    public function handleProviderCallback(Request $request)
    {
        $twitterUser = Socialite::driver('twitter')->user();
        $user = User::firstOrCreate([
            'account_id' => $twitterUser->getId(),
        ],[
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'name' => $twitterUser->getName(),
            'nickname' => $twitterUser->getNickname(),
            'twitter_token' => $twitterUser->token,
            'twitter_token_secret' => $twitterUser->tokenSecret,
            'title' => '？？？？？？？？',
            'sns_img_url' => str_replace_last('_normal', '', $twitterUser->getAvatar()),
            'twitter_followers_count' => $twitterUser->user['followers_count'],
            'description1' => '？？？？？？？？？？？？？？？？？？？？？',
            'description2' => '？？？？？？？？？？？？？？？？？？？？？',
            'description3' => '？？？？？？？？？？？？？？？？？？？？？',
        ]);
        $user->ip_address = $_SERVER['REMOTE_ADDR'];
        $user->save();

        // 初回ログイン&生成時
        if($user->wasRecentlyCreated){
            $items = [
                ['sentence1'=> 'こんにちわ'.$twitterUser->getName().'です。',
                'sentence2'=> $user->serial_number.'番目のツイモンとして誕生しました。',
                'sentence3'=> '私の戦闘力は'.$twitterUser->user['followers_count'].'です。',]
            ];
              
            $user->talks()->createMany($items);

            $arr_prof = [
                'name' => $user->name,
                'title' => $user->title,
                'description1' => $user->description1,
                'description2' => $user->description2,
                'description3' => $user->description3
            ];

            // SNS画像生成
            $sns_url = $user->createSnsImage($twitterUser);

            // OGP画像生成
            $ogp_url = $user->createOgpImage($arr_prof,$user,$twitterUser);
        }
        return [
            'access_token' => $user->createToken('twimonToken')->accessToken,
            'me' => $user
        ];
    }
}
