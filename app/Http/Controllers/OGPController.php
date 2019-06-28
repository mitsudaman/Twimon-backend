<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class OGPController extends Controller
{
    public function index($id = "")
    {
        // return Socialite::driver('twitter')->redirect();
        return view('ogp/index', ['id' => $id]);
    }

     /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback()
    {
        $user = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
        return [
            'user'         => $user,
        ];
    }
}
