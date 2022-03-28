<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'path' => $this->resource,
            'original_name' => basename($this->resource),
            'base64_name' => base64_encode($this->resource),
            'url' => Storage::disk('public')->url($this->resource),
            'mime' => mime_content_type(Storage::disk('public')->path(str_replace('/', '\\', $this->resource)))
        ];
    }
}
