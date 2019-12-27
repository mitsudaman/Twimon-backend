<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serial_number', 
        'account_id', 
        'name', 
        'nickname', 
        'twitter_token',
        'twitter_token_secret',
        'title',
        'type1',
        'type2',
        'feature1',
        'feature1_content',
        'feature2',
        'feature2_content',
        'description1',
        'description2',
        'description3',
        'url1_name',
        'url1',
        'url2_name',
        'url2',
        'url3_name',
        'url3',
        'url4_name',
        'url4',
        'url5_name',
        'url5',
        'sns_img_url',
        'ogp_img_url',
        'hall_of_fame_flg',
        'legend_flg',
        'twitter_followers_count'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function updateUserProf(array $arr_user_prof,object $twitterUser,String $sns_url,String $ogp_url)
    {
        // Userプロフアップデート
        $data = [
            'name' => $arr_user_prof['name'],
            'nickname' => $twitterUser->getNickname(),
            'title' => $arr_user_prof['title'],
            'type1' => $arr_user_prof['type1'],
            'type2' => $arr_user_prof['type2'],
            'description1' => $arr_user_prof['description1'],
            'description2' => $arr_user_prof['description2'],
            'description3' => $arr_user_prof['description3'],
            'sns_img_url' => $sns_url,
            'ogp_img_url' => $ogp_url,
            'twitter_followers_count' => $twitterUser->user['followers_count']
        ];

        $this->fill($data)->save();
        return ;
    }

    public function updateUserDetail(array $arr_user_detail)
    {
        // User詳細アップデート
        $data = [
            'url1_name' => $this->get_info_by_curl($arr_user_detail['url1'])[0],
            'url1' => $arr_user_detail['url1'],
            'url2_name' => $this->get_info_by_curl($arr_user_detail['url2'])[0],
            'url2' => $arr_user_detail['url2'],
            'url3_name' => $this->get_info_by_curl($arr_user_detail['url3'])[0],
            'url3' => $arr_user_detail['url3'],
            'url4_name' => $this->get_info_by_curl($arr_user_detail['url4'])[0],
            'url4' => $arr_user_detail['url4'],
            'url5_name' => $this->get_info_by_curl($arr_user_detail['url5'])[0],
            'url5' => $arr_user_detail['url5'],
        ];

        $this->fill($data)->save();
        return;
    }


    public function talks():HasMany
    {
        return $this->hasMany('App\Talk');
    }

   /**
    * スキしてくれた人リスト 
    */
    public function likes(): HasMany
    {
        return $this->hasMany('App\Like','like_user_id');
    }
    protected $appends = ['liked'];

    public function getLikedAttribute()
    {
        $current_user = auth()->guard('api')->user();
        if(is_null($current_user)){
            return false;
        }
        $collection = collect($this->likes);
        return $collection->where('user_id', $current_user->id)->isNotEmpty();
    }

    public function get_info_by_curl(?String $url){
        $title = '';
        $image = '';

        if(!$url) return [$title,$image];

        $conn = curl_init($url);// urlは対象のページ
        curl_setopt($conn, CURLOPT_HEADER, 0);
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);// exec時に出力させない
        $res = curl_exec($conn);
        $errno = curl_errno($conn);
        $httpcode = curl_getinfo($conn, CURLINFO_HTTP_CODE);
        curl_close($conn);
        if(!($httpcode=='200' && $errno == '0')) return [$title,$image];

        $dom_document = new \DOMDocument();
        $from_encoding = mb_detect_encoding($res, ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS'], true);
        if (!$from_encoding)
        {
            $from_encoding = 'SJIS';
        }
        @$dom_document->loadHTML(mb_convert_encoding($res, 'HTML-ENTITIES', $from_encoding));
        $xml_object = simplexml_import_dom($dom_document);

        $title = $this->getInfoFromXpath('title', $xml_object);
        $image = $this->getInfoFromXpath('image', $xml_object);

        return [$title,$image];
    }

    public function getInfoFromXpath(string $path_name,object $xml_object){
        $meta = "";
        $og_xpath = $xml_object->xpath('//meta[@property="og:{$path_name}"]/@content');
        $xpath = $xml_object->xpath("//{$path_name}");
        if (!empty($og_xpath))
        {
            $meta = (string)$og_xpath[0];
        }
        if ($meta === '' && $xpath)
        {
            $meta = (string)$xpath[0];
        }
        return $meta;
    }

    // SNS画像作成
    public function createSnsImage(object $twitterUser)
    {
        $url = str_replace_last('_normal','',str_replace_last('_normal', '', $twitterUser->getAvatar()));
        $img = \Image::make($url);

        // ローカル保存用
        // return $this->putImageToLocal('app/images/sns.png',$img);

        // S3保存用
        $savePath = env('APP_ENV').'/uploads/avatar/'.$this->id.'.png';
        return $this-> putImageToS3($savePath,$img);
    }

    // OGP画像作成
    public function createOgpImage(array $args,User $user,object $twitterUser)
    {
        $path = storage_path('app/images/ogp.png');
        $img = \Image::make($path);

        // アバター画像
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
        $interval = 36;

        // // ニックネーム
        // $fontSize=$this->calcProfFontSize('@'.$twitterUser->nickname);
        // $img->text('@'.$twitterUser->nickname, $prof_x_point, $prof_y_point, function($font) use ($fontSize){
        //     $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
        //     $font->size($fontSize);
        //     $font->color('#000');
        // });

        // なまえ
        $fontSize=$this->calcProfFontSize($args['name']);
        $img->text($args['name'], $prof_x_point, $prof_y_point, function($font) use ($fontSize){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size($fontSize);
            $font->color('#000');
        });

        // タイトル
        $prof_y_point += $interval;
        if($args['title']){
            $fontSize=$this->calcProfFontSize($args['title']);
            $img->text($args['title'], $prof_x_point, $prof_y_point, function($font) use ($fontSize){
                $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
                $font->size($fontSize);
                $font->color('#000');
            });
        }

        // 戦闘力
        $prof_y_point += $interval;
        $img->text("戦闘力", $prof_x_point, $prof_y_point, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->color('#000');
        });
        $img->text($twitterUser->user['followers_count'], $prof_x_point+150, $prof_y_point, function($font){
            $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
            $font->size(20);
            $font->align('center');
            $font->color('#000');
        });

        $type1 = $args['type1'];
        $type_y1_point = $prof_y_point + 18;
        $type_x1_point = $prof_x_point;
        $type_width = 110;
        if($type1){
            $color = config('const.type_color')[$type1];
            $img->rectangle($type_x1_point, $type_y1_point, $type_x1_point + $type_width, $type_y1_point + $interval, function ($draw) use ($color) {
                $draw->background($color);
            });
            $img->text($type1, $type_x1_point + 55, $type_y1_point + 29, function($font){
                $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
                $font->size(20);
                $font->align('center');
                $font->color('#fff');
            });
        }
        $type2 = $args['type2'];
        if($type2){
            $type_x1_point +=150;
            $color = config('const.type_color')[$type2];
            $img->rectangle($type_x1_point, $type_y1_point, $type_x1_point + $type_width, $type_y1_point + $interval, function ($draw) use ($color) {
                $draw->background($color);
            });
            $img->text($type2, $type_x1_point + 55, $type_y1_point + 29, function($font){
                $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
                $font->size(20);
                $font->align('center');
                $font->color('#fff');
            });
        }

        // パラメータ確認用
        // ハンバーガーキッドｱあああああいうえおか はば39 Byte60 長さ20
        // $strWidth = mb_strwidth($args['title']);
        // $img->text('はば: '.mb_strwidth($args['title']).' Byte: '.strlen($args['title']).' 長さ: '.mb_strlen($args['title']), $prof_x_point, $prof_y_point + 105, function($font){
        //     $font->file(storage_path('app/fonts/PixelMplus10-Regular.ttf'));
        //     $font->size(20);
        //     $font->color('#000');
        // });

        // せつめい
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
        // return $this->putImageToLocal('app/images/ogp2.png',$img);

        // S3保存用
        $image_name = '';
        if($user->ogp_img_url) $image_name = explode("uploads/ogp/",$user->ogp_img_url)[1];
        else $image_name = ((string) Str::uuid()).'.png';
        $savePath = env('APP_ENV').'/uploads/ogp/'.$image_name;
        return $this-> putImageToS3($savePath,$img);
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

    public function putImageToLocal(string $url,object $img){
        $save_path = storage_path($url);
        $img->save($save_path);
        return $save_path;
    }

    public function putImageToS3(string $url,object $img){
        $path = Storage::disk('s3')->put($url, $img->stream(), 'public');
        $url = Storage::disk('s3')->url($url);
        return $url;
    }
}
