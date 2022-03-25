<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MyCommentThreadResource extends JsonResource
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
            'thumbnail' => $this->thumbnail,
            'posted_at' => $this->created_at->diffForHumans(),
        ];
    }
}
