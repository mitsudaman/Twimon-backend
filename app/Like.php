<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'liked_user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
