<?php

namespace App\GraphQL\Mutations;

use App\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UpdateUserProf
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
        $user = auth()->guard('api')->user();
        $arr_prof = array_get($args,'input');
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret($user->twitter_token, $user->twitter_token_secret);

        // SNS画像生成
        $sns_url = $user->createSnsImage($twitterUser);

        // OGP画像生成
        $ogp_url = $user->createOgpImage($arr_prof,$user,$twitterUser);
        
        // Userプロフアップデート
        $user->updateUserProf($arr_prof,$twitterUser,$sns_url,$ogp_url);

        return $user;
    }
}
