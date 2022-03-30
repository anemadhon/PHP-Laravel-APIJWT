<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Thread;
use App\Enums\LikeStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ThreadRequest;
use App\Http\Resources\ThreadCollection;
use App\Http\Resources\ErrorResponseCollection;
use App\Services\LikeService;

class ThreadController extends Controller
{
    public function __construct()
    {
        return auth()->shouldUse('api');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new ThreadCollection(Thread::with([
            'user',
            'likes', 'likes.user', 
            'unlikes', 'unlikes.user',
            'comments', 'comments.user'
        ])->paginate(6), 200, 'All Posts Shown Successfully', 'index');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ThreadRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $title = $request->validated()['title'];
            $thumbnail = $request->file('thumbnail');
            $validated['thumbnail'] = $thumbnail->storeAs('images', "thumbnail/{$title}/{$thumbnail->getClientOriginalName()}", 'public');
        }

        $thread = auth()->user()->threads()->create($validated);

        return new ThreadCollection($thread, 200, 'Data Posted Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show(Thread $thread)
    {
        return new ThreadCollection($thread->load([
            'user',
            'likes', 'likes.user', 
            'unlikes', 'unlikes.user',
            'comments', 'comments.user'
        ]), 200, 'Single Post Shown Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadRequest $request, Thread $thread)
    {
        $this->authorize('modify-threads', $thread);

        $validated = $request->validated();

        if ($request->hasFile('thumbnail')) {
            $title = $request->validated()['title'];
            $thumbnail = $request->file('thumbnail');
            $validated['thumbnail'] = $thumbnail->storeAs('images', "thumbnail/{$title}/{$thumbnail->getClientOriginalName()}", 'public');
        }

        $thread->update($validated);

        return new ThreadCollection($thread, 200, 'Post Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread)
    {
        $this->authorize('modify-threads', $thread);

        $thread->delete();

        return new ThreadCollection($thread, 200, 'Post Deleted Successfully');
    }

    public function byCategory(string $category)
    {
        $threads = Thread::with([
            'user',
            'likes', 'likes.user', 
            'unlikes', 'unlikes.user',
            'comments', 'comments.user'
        ])->where('category', 'like', "%{$category}%")->paginate(6);

        if ($threads->count() === 0) {
            return new ErrorResponseCollection(404, 'Not Found', [
                'message' => "Posts by {$category} Not Found"
            ]);
        }

        return new ThreadCollection($threads, 200, "Posts by {$category} Shown Successfully", 'index');
    }

    public function likes(Thread $thread)
    {
        $status = (new LikeService())->checkAvailablityUser($thread->id, auth()->id());

        if ($status === 'not available') {
            return response()->json([
                'status' => true,
                'message' => "You're Like this Thread before",
                'data' => null
            ], 200);
        }

        auth()->user()->likes()->create([
            'status' => LikeStatus::like,
            'thread_id' => $thread->id
        ]);

        return new ThreadCollection(auth()->user()->likes->load([
            'thread', 
            'thread.likes', 'thread.likes.user',
            'thread.unlikes', 'thread.unlikes.user',
            'thread.comments', 'thread.comments.user'
        ])->pluck('thread'), 200, 'Your Likes Threads Shown Successfully');
    }
    
    public function unlikes(Thread $thread)
    {
        $status = (new LikeService())->checkAvailablityUser($thread->id, auth()->id());

        if ($status === 'not available') {
            return response()->json([
                'status' => true,
                'message' => "You're Unlike this Thread before",
                'data' => null
            ], 200);
        }

        auth()->user()->likes()->create([
            'status' => LikeStatus::unlike,
            'thread_id' => $thread->id
        ]);

        return new ThreadCollection(auth()->user()->likes->load([
            'thread', 
            'thread.likes', 'thread.likes.user',
            'thread.unlikes', 'thread.unlikes.user',
            'thread.comments', 'thread.comments.user'
        ])->pluck('thread'), 200, 'Your Unlikes Threads Shown Successfully');
    }
}
