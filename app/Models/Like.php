<?php

namespace App\Models;

use App\Enums\LikeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        'user_id',
        'thread_id'
    ];

    protected $cast = [
        'status' => LikeStatus::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
}
