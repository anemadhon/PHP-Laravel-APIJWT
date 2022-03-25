<?php

namespace App\Http\Resources;

use App\Http\Resources\MyCommentResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MyCommentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'success' => true,
            'message' => 'Your Comments Shown Successfully',
            'data' => MyCommentResource::collection($this->collection)
        ]; 
    }
}
