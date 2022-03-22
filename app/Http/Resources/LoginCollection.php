<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoginCollection extends ResourceCollection
{
    public $statusCode;
    public $message;
    public $token;
    public $error;

    public function __construct(int $statusCode, string $message, ?array $token, ?array $error)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->token = $token;
        $this->error = $error;
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
            'success' => ($this->statusCode === 200 ? true : false),
            'message' => $this->message,
            'data' => ($this->statusCode === 200 ? new UserResource($request->user()) : null),
            'token' => $this->when(!is_null($this->token), $this->token),
            'error' => $this->when(!is_null($this->error), $this->error)
        ];
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))
            ->toResponse($request)
            ->setStatusCode($this->statusCode);
    }
}
