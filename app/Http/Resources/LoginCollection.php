<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class LoginCollection extends ResourceCollection
{
    public $statusCode;
    public $message;
    public $token;

    public function __construct(int $statusCode, $message = null, $token = null)
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->token = $token;
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
            'message' => $this->when(!is_null($this->message), $this->message),
            'data' => ($this->statusCode === 200 ? new UserResource($request->user()) : null),
            'token' => $this->when(!is_null($this->token), $this->token)
        ];
    }

    public function toResponse($request)
    {
        return (new ResourceResponse($this))
            ->toResponse($request)
            ->setStatusCode($this->statusCode);
    }
}
