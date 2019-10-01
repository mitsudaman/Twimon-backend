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
        $like_user = User::find($args['like_user_id']);
        $like = \Auth::user()->likes()->where('like_user_id',$args['like_user_id'])->first();
        
        if(empty($like)){
            $like = Like::create([
                'user_id' => \Auth::user()->id,
                'like_user_id' => $args['like_user_id']
            ]);            
            $like_user->increment('like_ct', 1);
        }else{
            $like = $like->delete();
            $like_user->decrement('like_ct', 1);
        }

        return $like;
    }
}
