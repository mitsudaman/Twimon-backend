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
            'url1_name' => $arr_user_detail['url1_name'],
            'url1' => $arr_user_detail['url1'],
            'url2_name' => $arr_user_detail['url2_name'],
            'url2' => $arr_user_detail['url2'],
            'url3_name' => $arr_user_detail['url3_name'],
            'url3' => $arr_user_detail['url3'],
            'url4_name' => $arr_user_detail['url4_name'],
            'url4' => $arr_user_detail['url4'],
            'url5_name' => $arr_user_detail['url5_name'],
            'url5' => $arr_user_detail['url5'],
        ];

        $this->fill($data)->save();
        return ;
    }

    public function talks():HasMany
    {
        return $this->hasMany('App\Talk');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    protected $appends = ['liked'];

    public function getLikedAttribute()
    {
        $current_user = auth()->guard('api')->user();
        if(is_null($current_user)){
            return false;
        }
        $collection = collect($this->likes);
        return $collection->where('liked_user_id', $current_user->id)->isNotEmpty();
    }
}
