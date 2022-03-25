<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Resources\LoginCollection;
use App\Http\Resources\MyCommentCollection;
use App\Http\Resources\ThreadCollection;

class UserAuthenticatedController extends Controller
{
    public function getProfile()
    {
        return new LoginCollection(200, 'Your Profile Shown Successfully');
    }

    public function updateProfile(ProfileRequest $request)
    {
        auth()->user()->update($request->validated());

        return new LoginCollection(200, 'Your Profile Updated Successfully');
    }

    public function threads()
    {
        return new ThreadCollection(auth()->user()->threads, 200, 'Your Threads Shown Successfully', 'index');
    }
    
    public function comments()
    {
        return new MyCommentCollection(auth()->user()->comments);
    }
}
