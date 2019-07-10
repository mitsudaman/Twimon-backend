<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\User;
use App\Like;

class AddOrDeleteLikeUser
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
        $like = like::where('user_id',$args['user_id'])
        ->where('liked_user_id',\Auth::user()->id)->first();
        
        if(empty($like)){
            $like = Like::create([
                'user_id' => $args['user_id'],
                'liked_user_id' => \Auth::user()->id
            ]);
        }else{
            $like = $like->delete();
        }

        return $like;

        // $user = User::find($args['user_id']);
        // if($user->liked){
        //     $user->likes()->where('liked_user_id',\Auth::user()->id)->first()->delete();
        // }else{
        //     $user->likes()->create([
        //         'liked_user_id' => \Auth::user()->id,
        //     ]);
        // }

        // $user = User::find($args['user_id']);
        // return $user->liked;

    }
}
