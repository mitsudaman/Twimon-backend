<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use DB;

class OGPController extends Controller
{
    public function index($id = "")
    {
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        // print_r($twitterUser->user['followers_count']);
        $user = User::find($id);
        return view('ogp/index', ['user' => $user]);
    }

     /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        // print_r($twitterUser->getAvatar());


        $user = User::firstOrCreate([
            'account_id' => $twitterUser->getId(),
        ],[
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'name' => $twitterUser->getName(),
            'img_src' => $twitterUser->getAvatar(),
        ]);

        return [
            'user'         => $twitterUser,
            'access_token' => $user->createToken('twimonToken')->accessToken,
        ];
    }
}
