<?php

namespace App\GraphQL\Queries;
use App\User;
use App\Like;


use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetLikeUsers
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
    // public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    // {
    //     // TODO implement the resolver
    // }
    public static function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $likes = Like::where('user_id',\Auth::user()->id)->get();
        $like_user_ids = $likes ->map(function ($item, $key) {
            return $item->like_user_id;
        });
        $like_users = User::whereIn('id',$like_user_ids)->paginate($args['perPage'],['*'], 'page', $args['page']);
        return [
            'likeUsers' => $like_users,
            'paginatorInfo' => [
                'currentPage' => $like_users->currentPage(),
                'lastPage' => $like_users->lastPage()
            ]
        ];
    }
}
