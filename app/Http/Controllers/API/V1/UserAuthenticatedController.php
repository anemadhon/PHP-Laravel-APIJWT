<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\LoginCollection;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\MyCommentCollection;

class UserAuthenticatedController extends Controller
{
    public function getProfile()
    {
        return new LoginCollection(200, 'Your Profile Shown Successfully');
    }

    public function updateProfile(ProfileRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
            $username = auth()->user()->username;
            $avatar = $request->file('avatar');
            $validated['avatar'] = $avatar->storeAs('images', "avatar/{$username}/{$avatar->getClientOriginalName()}", 'public');
        }

        auth()->user()->update($validated);

        return new LoginCollection(200, 'Your Profile Updated Successfully');
    }

    public function threads()
    {
        return new ThreadCollection(auth()->user()->threads()->with([
            'user',
            'likes', 'likes.user', 
            'unlikes', 'unlikes.user',
            'comments', 'comments.user'
        ])->paginate(6), 200, 'Your Threads Shown Successfully', 'index');
    }
    
    public function likeThreads()
    {
        return new ThreadCollection(auth()->user()->likes()->with([
            'thread', 
            'thread.likes', 'thread.likes.user',
            'thread.unlikes', 'thread.unlikes.user',
            'thread.comments', 'thread.comments.user'
        ])->paginate(6)->pluck('thread'), 200, 'Your Likes Threads Shown Successfully', 'index');
    }
    
    public function unlikeThreads()
    {
        return new ThreadCollection(auth()->user()->unlikes()->with([
            'thread', 
            'thread.likes', 'thread.likes.user',
            'thread.unlikes', 'thread.unlikes.user',
            'thread.comments', 'thread.comments.user'
        ])->paginate(6)->pluck('thread'), 200, 'Your Unlikes Threads Shown Successfully', 'index');
    }
    
    public function comments()
    {
        return new MyCommentCollection(auth()->user()->comments()->with('thread')->paginate(6));
    }
}
