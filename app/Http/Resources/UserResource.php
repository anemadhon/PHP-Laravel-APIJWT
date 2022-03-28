<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'avatar' => (is_null($this->avatar) ? 
                'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=random&rounded=true&bold=true' : 
                new ImageResource($this->avatar)
            ),
            'join_thread_at' => $this->created_at->diffForHumans()
        ];
    }
}
