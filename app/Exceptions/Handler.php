<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
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
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('it4788/*') || $request->is('api/*')) {
                return response()->json([
                    'code' => config('response_code.token_invalid'),
                    'message' => __('messages.token_invalid'),
                ]);
            }
        });

        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('it4788/*') || $request->is('api/*')) {
                return response()->json([
                    'code' => config('response_code.not_access'),
                    'message' => __('messages.not_access'),
                ]);
            }
        });

        $this->renderable(function (QueryException $e, $request) {
            if ($request->is('it4788/*') || $request->is('api/*')) {
                return response()->json([
                    'code' => config('response_code.can_not_connect_database'),
                    'message' => __('messages.can_not_connect_database'),
                ]);
            }
        });
    }
}
