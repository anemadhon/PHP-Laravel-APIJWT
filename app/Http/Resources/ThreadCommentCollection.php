<?php

namespace App\Http\Resources;

use App\Http\Resources\CommentResource;
use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ThreadCommentCollection extends ResourceCollection
{
    public $resource;
    public $statusCode;
    public $message;

    public function __construct($resource, int $statusCode, string $message)
    {
        parent::__construct($resource);

        $this->resource = $resource;
        $this->statusCode = $statusCode;
        $this->message = $message;
    }

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
            'message' => $this->message,
            'data' => new CommentResource($this->resource)
        ]; 
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))
            ->toResponse($request)
            ->setStatusCode($this->statusCode);
    }
}
