<?php

namespace App\Exceptions;

use App\Support\Api\ApiResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
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
        // Versioned API: always the JSON error envelope, never stack traces
        // (even with APP_DEBUG=true).
        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/v1/*', 'api/v1')) {
                return $this->renderApiException($e, $request);
            }
        });
    }

    protected function renderApiException(Throwable $e, Request $request)
    {
        if ($e instanceof ValidationException) {
            return ApiResponse::error($request, 422, 'validation_failed', 'The request parameters are invalid.', $e->errors());
        }
        if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
            return ApiResponse::error($request, 404, 'not_found', 'The requested resource was not found.');
        }
        if ($e instanceof MethodNotAllowedHttpException) {
            return ApiResponse::error($request, 405, 'method_not_allowed', 'This API is read-only. Use GET.', null, $e->getHeaders());
        }
        if ($e instanceof ThrottleRequestsException) {
            return ApiResponse::error($request, 429, 'rate_limited', 'Too many requests. Retry after the time given in Retry-After.', null, $e->getHeaders());
        }
        if ($e instanceof HttpExceptionInterface && $e->getStatusCode() < 500) {
            return ApiResponse::error($request, $e->getStatusCode(), 'http_error', $e->getMessage() ?: 'Request failed.', null, $e->getHeaders());
        }

        // Unexpected errors are reported (logged) by the framework; the
        // response only carries the request id for correlation.
        return ApiResponse::error($request, 500, 'server_error', 'An unexpected error occurred. Quote the request_id when reporting it.');
    }
}
