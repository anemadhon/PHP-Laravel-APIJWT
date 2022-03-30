<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ThreadCommentRequest;
use App\Http\Resources\ThreadCommentCollection;
use App\Models\Thread;
use App\Models\ThreadComment;

class ThreadCommentController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Thread $thread, ThreadCommentRequest $request)
    {
        $comment = auth()->user()->comments()->create($request->validated() + ['thread_id' => $thread->id]);

        return new ThreadCommentCollection($comment, 200, 'Comment Posted Successfully');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ThreadComment  $threadComment
     * @return \Illuminate\Http\Response
     */
    public function update(ThreadCommentRequest $request, Thread $thread, ThreadComment $comment)
    {
        $this->authorize('modify-comments', $comment);
        
        $comment->update($request->validated());

        return new ThreadCommentCollection($comment, 200, 'Comment Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ThreadComment  $threadComment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Thread $thread, ThreadComment $comment)
    {
        $this->authorize('modify-comments', $comment);

        $comment->delete();

        return new ThreadCommentCollection($comment, 200, 'Comment Deleted Successfully');
    }
}
