<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Talk extends Model
{
    use SoftDeletes;

    /**
     * 日付へキャストする属性
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'kind', 
        'sentence1', 
        'sentence2', 
        'sentence3', 
        'sentence4', 
        'sentence5', 
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
