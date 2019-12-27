<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Laravel\Socialite\Facades\Socialite;
use App\User;
use DB;

class AuthenticateUserGql
{
    /**
     * Return a value for the field.
     *
     * @param  null  $rootValue Usually contains the result returned from the parent field. In this case, it is always `null`.
     * @param  mixed[]  $args The arguments that were passed into the field.
     * @param  \Nuwave\Lighthouse\Support\Contracts\GraphQLContext  $context Arbitrary data that is shared between all fields of a single query.
     * @param  \GraphQL\Type\Definition\ResolveInfo  $resolveInfo Information about the query itself, such as the execution state, the field name, path to the field from the root, and more.
     * @return mixed
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));

        $user = User::firstOrCreate([
            'account_id' => $twitterUser->getId(),
        ],[
            'serial_number' => DB::table('users')->max('serial_number')+1,
            'name' => $twitterUser->getName(),
            'nickname' => $twitterUser->getNickname(),
            'type1' => 'ノーマル',
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


            $arrProf = [
                'name' => $user->name,
                'title' => $user->title,
                'type1' => 'ノーマル',
                'type2' => '',
                'description1' => $user->description1,
                'description2' => $user->description2,
                'description3' => $user->description3
            ];

            // SNS画像生成
            $sns_url = $user->createSnsImage($twitterUser);

            error_log($sns_url);
            // OGP画像生成
            $ogp_url = $user->createOgpImage($arrProf,$user,$twitterUser);
       

            // SNS・OGP画像更新
            $user->sns_img_url = $sns_url;
            $user->ogp_img_url = $ogp_url;
            $user->save();
        }

        return [
            'access_token' => $user->createToken('twimonToken')->accessToken,
            'me' => $user
        ];
    }
}
