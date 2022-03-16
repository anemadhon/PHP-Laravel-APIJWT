<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreadComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
        'thread_id',
        'user_id'
    ];
}
