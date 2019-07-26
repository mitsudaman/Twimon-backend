<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use DB;

class OGPController extends Controller
{
    public function index(Request $request,$id = "")
    {
        // print $request->session()->all();
        // error_log(print_r($request->session()->all(), true));
        // return Socialite::driver('twitter')->redirect();
        // return Socialite::driver('twitter')->redirect()->getTargetUrl();
        // return view('ogp/index', ['id' => $id]);
        // print_r($request->session()->all(),true);
        error_log("---------------------------------------");
        $url =Socialite::with('twitter')->stateless()->redirect();
         error_log(print_r($request->session()->all(), true));
        // return $request->session()->all();
        
        return Socialite::with('twitter')->stateless()->redirect()->getTargetUrl();
        // return Socialite::with('twitter')->stateless()->redirect();
    }

     /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {

        error_log("---------------------------------------");
        // return [
        //     'user'         => "user",
        //     'access_token' => "access_token",
        // ];
        
        // error_log(print_r(Socialite::driver('twitter')->user(),true));
        $twitterUser = Socialite::driver('twitter')->user();

        // $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
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
