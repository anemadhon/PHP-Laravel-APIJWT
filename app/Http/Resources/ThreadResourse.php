<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\JsonResource;

class ThreadResourse extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'category' => $this->category,
            'slug' => $this->slug,
            'posted_by' => new UserResource($this->user),
            'posted_at' => $this->created_at->diffForHumans(),
            'has_comments' => [
                'status' => $this->comments->count() > 0 ? true : false,
                'count' => $this->comments->count(),
                'data' => CommentResource::collection($this->comments)
            ]
        ];
    }
}
