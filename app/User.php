<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
