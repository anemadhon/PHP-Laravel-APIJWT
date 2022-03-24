<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\Json\ResourceResponse;

class ThreadCollection extends ResourceCollection
{
    public $resource;
    public $statusCode;
    public $message;
    public $method;

    public function __construct($resource, int $statusCode, string $message, string $method = '')
    {
        parent::__construct($resource);

        $this->resource = $resource;
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->method = $method;
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
            'data' => ($this->statusCode === 200 ? ($this->method === 'index' ? ThreadResourse::collection($this->resource) : new ThreadResourse($this->resource)) : null)
        ];
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))
            ->toResponse($request)
            ->setStatusCode($this->statusCode);
    }
}
