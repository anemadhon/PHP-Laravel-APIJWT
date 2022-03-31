<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ErrorResponseCollection extends ResourceCollection
{
    public $statusCode;
    public $message;
    public $error;

    public function __construct(int $statusCode, string $message, array $error)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->error = $error;
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'success' => false,
            'message' => $this->message,
            'data' => null,
            'errors' => $this->error
        ];
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))
            ->toResponse($request)
            ->setStatusCode($this->statusCode);
    }
}
