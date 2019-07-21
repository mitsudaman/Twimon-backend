<?php

namespace App\GraphQL\Mutations;

use App\User;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class UpdateUser
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
        // 画像生成
        $this->creageImage($args);

        // Userアップデート
        $data = [
            'name' => $args['name'], 
            // 'title' => $args['title'],
            'feature1' => $args['feature1'],
            // 'feature1_content' => $args['feature1_content'],
            // 'feature2' => $args['feature2'],
            // 'feature2_content' => $args['feature2_content'],
            'description' => $args['description'],
        ];
        $user = auth()->guard('api')->user();
        $user->update($data);
        return $user;
    }

    public function creageImage(array $args)
    {
        $path = storage_path('app/images/ogp.png');
        $img = \Image::make($path);
        $text = $args['description'];

        $c = mb_strlen($text);
        
        $img->text($text, 50, 100, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(30);
            $font->color('#000');
        });
        $img->text($text, 50, 175, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(30);
            $font->color('#000');
        });
        $img->text($text, 50, 250, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(30);
            $font->color('#000');
        });

        $save_path = storage_path('app/images/ogp2.png');
        $img->save($save_path);
    }
}
