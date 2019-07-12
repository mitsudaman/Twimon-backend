<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Talk extends Model
{
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
    ];

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
