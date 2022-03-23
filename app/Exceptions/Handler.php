<?php

namespace App\Exceptions;

use App\Http\Resources\ErrorResponseColection;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response(new ErrorResponseColection(404, 'Not Found', [
                    'message' => 'Data or object not found'
                ]));
            }
        });
        
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response(new ErrorResponseColection(405, 'Method Not Allowed', [
                    'message' => 'Your method was incorrect'
                ]));
            }
        });
    }
}