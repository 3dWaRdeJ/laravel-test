<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

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
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        switch ($exception) {
            case $exception instanceof ValidationException:
                /** @var ValidationException $exception */
                return response([
                    'success' => false,
                    'message' => 'Validation failed',
                    'fails' => $exception->validator->errors()->messages()
                ], 422, ['Content-Type' => 'application/json']);
                break;

            case $exception instanceof EmployeeException:
            case $exception instanceof PositionException:
                return response([
                    'success' => false,
                    'message' => $exception->getMessage()
                ], 409, ['Content-Type' => 'application/json']);
                break;

            default:
                return parent::render($request, $exception);

        }
    }
}
