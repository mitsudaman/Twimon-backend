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
        $ogp_url = $this->creageImage($arr_prof,$user);
        // $ogp_url = $this->createImageFromFront($arr_prof);
        
        // Userアップデート
        $data = [
            'name' => $arr_prof['name'],
            'title' => $arr_prof['title'],
            'feature1' => $arr_prof['feature1'],
            'feature1_content' => $arr_prof['feature1_content'],
            'feature2' => $arr_prof['feature2'],
            'feature2_content' => $arr_prof['feature2_content'],
            'description1' => $arr_prof['description1'],
            'description2' => $arr_prof['description2'],
            'description3' => $arr_prof['description3'],
            'ogp_img_url' => $ogp_url,
        ];

        $user->update($data);
        return $user;
    }

    public function creageImage(array $args,User $user)
    {
        $path = storage_path('app/images/ogp.png');
        $img = \Image::make($path);
        $twitterUser = Socialite::driver('twitter')->userFromTokenAndSecret(env('TWITTER_ACCESS_TOKEN'), env('TWITTER_ACCESS_TOKEN_SECRET'));

        // 画像 ゾーン
        $path2 = str_replace_last('_normal','',str_replace_last('_normal', '', $twitterUser->getAvatar()));
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
        $prof_x_point = 280;
        $prof_y_point = 58;

        $fontSize=$this->calcProfFontSize('@'.$twitterUser->nickname);
        $img->text('@'.$twitterUser->nickname, $prof_x_point, $prof_y_point, function($font) use ($fontSize){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size($fontSize);
            $font->color('#000');
        });
        $fontSize=$this->calcProfFontSize($args['name']);
        $img->text($args['name'], $prof_x_point, $prof_y_point + 36, function($font) use ($fontSize){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size($fontSize);
            $font->color('#000');
        });
        $fontSize=$this->calcProfFontSize($args['title']);
        $img->text($args['title'], $prof_x_point, $prof_y_point + 72, function($font) use ($fontSize){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size($fontSize);
            $font->color('#000');
        });
        $img->text("戦闘力", $prof_x_point, $prof_y_point + 108, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });
        $img->text($twitterUser->user['followers_count'], $prof_x_point+150, $prof_y_point + 108, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->align('center');
            $font->color('#000');
        });

        // パラメータ確認用
        // ハンバーガーキッドｱあああああいうえおか はば39 Byte60 長さ20
        // $strWidth = mb_strwidth($args['title']);
        // $img->text('はば: '.mb_strwidth($args['title']).' Byte: '.strlen($args['title']).' 長さ: '.mb_strlen($args['title']), $prof_x_point, $prof_y_point + 105, function($font){
        //     $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
        //     $font->size(20);
        //     $font->color('#000');
        // });


        // せつめい
        // $text = $args['description'];

        // $c = mb_strlen($text);

        $img->text($args['description1'], 40, 225, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Bold.ttf'));
            $font->size(18);
            $font->color('#fff');
        });
        $img->text($args['description2'], 40, 260, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Bold.ttf'));
            $font->size(18);
            $font->color('#fff');
        });
        $img->text($args['description3'], 40, 295, function($font){
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
        $image = $args['file'];
        $image = str_replace('data:image/png;base64,', '', $image);
        $image = str_replace(' ', '+', $image);

         // ローカル保存用
        // \File::put(storage_path(). '/app/images/ogp3.png', base64_decode($image));
        // return "aaa";

        // S3保存用
        Storage::disk('s3')->put('/uploads/ogp/test2.png', base64_decode($image), 'public');
        $url = Storage::disk('s3')->url('uploads/ogp/test2.png');
        return "aaa";
    }
    public function calcProfFontSize(string $text){
        $strWidth = mb_strwidth($text);
        $fontSize=20;
        if($strWidth>36) $fontSize=13;
        else if($strWidth>35) $fontSize=14;
        else if($strWidth>33) $fontSize=16;
        else if($strWidth>30) $fontSize=17;
        else if($strWidth>27) $fontSize=18;
        return $fontSize;
    }
}
