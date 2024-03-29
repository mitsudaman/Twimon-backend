<?php

namespace App\GraphQL\Queries;
use App\User;
use App\Like;


use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class GetUsers
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
        $query = User::query();
        $name = $args['name'];
        if($name){
            $query->where('name', 'LIKE',"%$name%");
        }

        $withDescription = $args['withDescription'];
        if($withDescription){
            $query->where(function($query){
                // デフォルトの設定以外
                $query->where('description1', '<>','？？？？？？？？？？？？？？？？？？？？？')
                    ->orWhere('description2', '<>','？？？？？？？？？？？？？？？？？？？？？')
                    ->orWhere('description2', '<>','？？？？？？？？？？？？？？？？？？？？？');
            });
        }
        $talkEditedFlg = $args['talkEditedFlg'];
        if($talkEditedFlg){
            // trueの時だけ絞る
            $query->where('talk_edited_flg', '=',true);
        }

        $searchTypes = $args['searchTypes'];
        if(count($searchTypes) > 0){
            $query->where(function($query) use($searchTypes){
                $query->whereIn('type1',$searchTypes)
                    ->orWhereIn('type2',$searchTypes);
            });
        }
        $query->orderBy('id', 'desc');
        $users = $query->paginate($args['perPage'],['*'], 'page', $args['page']);
        return [
            'users' => $users,
            'paginatorInfo' => [
                'currentPage' => $users->currentPage(),
                'lastPage' => $users->lastPage()
            ]
        ];
    }
}
