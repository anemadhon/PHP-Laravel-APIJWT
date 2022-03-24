<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadComment extends Model
{
    use HasFactory, Sluggable;

    protected $fillable = [
        'body',
        'slug',
        'thread_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => ['thread_id', 'user_id'],
                'separator' => ''
            ]
        ];
    }
}
