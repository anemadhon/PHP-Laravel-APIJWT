<?php

namespace App\Exceptions;

use App\Http\Resources\ErrorResponseCollection;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $this->renderable(function (AccessDeniedHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response(new ErrorResponseCollection(403, 'Forbidden', [
                    'message' => 'Your action is unauthorized'
                ]), 403);
            }
        });

        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response(new ErrorResponseCollection(404, 'Not Found', [
                    'message' => 'Data or object not found'
                ]), 404);
            }
        });
        
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->wantsJson()) {
                return response(new ErrorResponseCollection(405, 'Method Not Allowed', [
                    'message' => 'Your method was incorrect'
                ]), 405);
            }
        });
        
        $this->renderable(function (\Exception $e, $request) {
            if ($request->wantsJson() && auth()->check()) {
                return response(new ErrorResponseCollection(500, 'Internal Error', [
                    'message' => 'Please contact administrator for more info'
                ]), 500);
            }
        });
        
        $this->renderable(function (QueryException $e, $request) {
            if ($request->wantsJson() && auth()->check()) {
                return response(new ErrorResponseCollection(500, 'Internal Error', [
                    'message' => 'Please contact administrator for more info'
                ]), 500);
            }
        });
    }
}
