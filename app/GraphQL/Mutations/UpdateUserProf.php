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

        // 画像生成
        // $ogp_url = $this->creageImage($arr_prof,$user);
        $ogp_url = $this->createImageFromFront($arr_prof);
        
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
            'ogp_img_url' => $ogp_url,
        ];

        if($arr_prof['sns_img_use_flg']){
            $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));
            $data = array_add($data,'sns_img_url',$twitterUser->getAvatar());
        }else{
        }

        $user->update($data);
        return $user;
    }

    public function creageImage(array $args,User $user)
    {
        $path = storage_path('app/images/ogp.png');
        $img = \Image::make($path);

        // 画像 ゾーン
        $path2 = str_replace_last('_normal','',$user->sns_img_url);
        $img2 = \Image::make($path2);
        $img2->resize(150, 150);
        $img->insert($img2, 'top-left', 70, 20);
        $img->text("No.".sprintf('%03d', $user->serial_number), 140, 190, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->align('center');
            $font->color('#000');
        });

        // プロフ
        $prof_y_point = 60;
        $img->text($args['name'], 300, $prof_y_point, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });
        $img->text('かめのこツイモン', 300, $prof_y_point + 35, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });
        $img->text('たかさ     0.5m', 300, $prof_y_point + 70, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });
        $img->text('おもさ     9.0ｋｇ', 300, $prof_y_point + 105, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });

        // せつめい
        $text = $args['description'];

        $c = mb_strlen($text);

        $img->text($text, 40, 225, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Bold.ttf'));
            $font->size(18);
            $font->color('#fff');
        });
        $img->text('おなかが すぐいたくなる。', 40, 260, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Bold.ttf'));
            $font->size(18);
            $font->color('#fff');
        });
        $img->text('1じかんに3かい トイレに いきたがる しゅうせいを もつ。', 40, 295, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Bold.ttf'));
            $font->size(18);
            $font->color('#fff');
        });

        // ローカル保存用
        $save_path = storage_path('app/images/ogp2.png');
        $img->save($save_path);
        return "aaa";


        // S3保存用
        Storage::disk('s3')->put('/uploads/ogp/test.png', $img->stream(), 'public');
        $url = Storage::disk('s3')->url('uploads/ogp/test.png');

        return $url;
        // $path = Storage::disk('s3')->put('/uploads/ogp/'.(string) Str::uuid().'.png', $img->stream(), 'public');

    }

    public function createImageFromFront(array $args){
        // ローカル保存用
        $file = $args['file'];
        $save_path = storage_path('ogp3.png');
        $file->storeAs('images','ogp3.png');
        return $file;

        // S3保存用
        Storage::disk('s3')->put('/uploads/ogp/test2.png', $file, 'public');
        $url = Storage::disk('s3')->url('uploads/ogp/test2.png');
        return $file;
    }
}
