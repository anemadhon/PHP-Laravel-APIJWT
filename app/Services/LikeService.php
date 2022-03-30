<?php

namespace App\Services;

use App\Models\Like;
use App\Models\User;
use App\Models\Thread;

class LikeService
{
    public function checkAvailablityUser(int $threadId, int $userId)
    {
        $likes = Like::where('thread_id', $threadId)->get();
        $status = 'available';

        if ($likes->count() === 0) {
            return $status;
        }

        foreach ($likes as $like) {
            if ($userId == $like->user_id) {
                $status = 'not available';
                break;
            }
        }

        return $status;
    }
}
