<?php

namespace App\Models;

use App\Enums\LikeStatus;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Thread extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'title', 
        'body',
        'category', 
        'slug',
        'thumbnail',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(ThreadComment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class)->where('status', LikeStatus::like);
    }
    
    public function unlikes()
    {
        return $this->hasMany(Like::class)->where('status', LikeStatus::unlike);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'onUpdate' => true
            ]
        ];
    }
}
