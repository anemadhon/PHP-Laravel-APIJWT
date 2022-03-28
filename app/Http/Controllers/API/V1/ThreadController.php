<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThreadRequest;
use App\Http\Resources\ThreadCollection;
use App\Models\Thread;
use Illuminate\Http\Request;

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
        return new ThreadCollection(Thread::all(), 200, 'All Posts Shown Successfully', 'index');
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
        return new ThreadCollection($thread, 200, 'Single Post Shown Successfully');
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
    public function destroy(ThreadRequest $thread)
    {
        $thread->delete();

        return new ThreadCollection($thread, 200, 'Post Deleted Successfully');
    }
}
