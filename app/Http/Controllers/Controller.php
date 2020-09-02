<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @var string[]
     */
    protected $defaultHeaders = [
        'Content-Type' => 'application/json'
    ];

    /**
     * @param string|null $message
     * @param mixed $data
     * @param int $code
     * @param array $headers
     * @return ResponseFactory|Application|Response
     */
    protected function response( ?string $message = null, $data = [], int $code = 200, array $headers = [])
    {
        $success = false;
        if (preg_match('/^2\d{2}$/', $code)) {
            $success = true;
        }

        $headers = array_merge($this->defaultHeaders, $headers);
        $responseArray = [
            'message' => $message,
            'success' => $success,
            'data' => $data
        ];

        if ($headers['Content-Type'] === 'application/json') {
            $responseArray = json_encode($responseArray, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return response($responseArray, $code, $headers);
    }
}
