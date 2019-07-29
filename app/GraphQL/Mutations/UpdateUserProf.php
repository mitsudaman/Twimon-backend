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
        $arr_prof = array_get($args,'input');

        // 画像生成
        $ogp_url = $this->creageImage($arr_prof);

        // Storage::disk('s3')->put('avatars/1', $fileContents);

        // return ;

        // Userアップデート
        $data = [
            'name' => $arr_prof['name'],
            'title' => $arr_prof['title'],
            'feature1' => $arr_prof['feature1'],
            'feature1_content' => $arr_prof['feature1_content'],
            'feature2' => $arr_prof['feature2'],
            'feature2_content' => $arr_prof['feature2_content'],
            'description' => $arr_prof['description'],
            'sns_img_use_flg' => $arr_prof['sns_img_use_flg'],
        ];

        if($arr_prof['sns_img_use_flg']){
            $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
            $data = array_add($data,'sns_img_src',$twitterUser->getAvatar());
        }else{
        }

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


        Storage::disk('s3')->put('/uploads/ogp/test.png', $img->stream(), 'public');
        $url = Storage::disk('s3')->url('uploads/ogp/test.png');

        return $url;
        // $path = Storage::disk('s3')->put('/uploads/ogp/'.(string) Str::uuid().'.png', $img->stream(), 'public');

    }
}