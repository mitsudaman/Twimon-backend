<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serial_number', 
        'account_id', 
        'name', 
        'title',
        'feature1',
        'feature1_content',
        'feature2',
        'feature2_content',
        'description',
        'sns_img_url',
        'ogp_img_url',
        'hall_of_fame_flg',
        'legend_flg'
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

    public function talks():HasMany
    {
        return $this->hasMany('App\Talk');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
    protected $appends = ['liked','like_ct'];

    public function getLikedAttribute()
    {
        $current_user = auth()->guard('api')->user();
        if(is_null($current_user)){
            return false;
        }
        $collection = collect($this->likes);
        return $collection->where('liked_user_id', $current_user->id)->isNotEmpty();
    }

    public function getLikeCtAttribute()
    {
        return $this->likes()->count();;
    }
}
