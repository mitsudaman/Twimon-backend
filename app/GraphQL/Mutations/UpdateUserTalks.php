<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use App\User;
use App\Like;
use App\Talk;

class UpdateUserTalks
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
        $user = User::find($args['id']);
        if($user->id==\Auth::user()->id){
            // Create
            if(array_has($args,'talks.create')){
                $arr_create = array_get($args,'talks.create');
                $user->talks()->createMany($arr_create);
            }
            
            // Update
            if(array_has($args,'talks.update')){
                $arr_update = array_get($args,'talks.update');
                foreach ($arr_update as $value) {
                    $talk = Talk::find($value['id']);
                    if($talk->user_id==\Auth::user()->id){
                        $data = [
                            'sentence1' => $value['sentence1'],
                            'sentence2' => $value['sentence2'],
                            'sentence3' => $value['sentence3'],
                        ];
                        $talk->update($data);
                    }else{
                        abort(403, 'Unauthorized action.');
                    }
                }
            }

            // Delete
            if(array_has($args,'talks.delete')){
                $arr_delete = array_get($args,'talks.delete');
                foreach ($arr_delete as $value) {
                    $talk = Talk::find($value);
                    if($talk->user_id==\Auth::user()->id){
                        $talk->delete();
                    }else{
                        abort(403, 'Unauthorized action.');
                    }
                }
            }
        }else{
            abort(403, 'Unauthorized action.');
        }

        return $user;
    }
}
