<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
     protected $fillable = [
        'user_id',
        'yandex_url',
        'organization_id',
        'name',
        'average_rating',
        'total_ratings',
        'total_reviews',
        'reviews_cache',
        'last_parsed_at',
    ];

    protected $casts = [
        'reviews_cache' => 'array',
        'last_parsed_at' => 'datetime',
        'average_rating' => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
