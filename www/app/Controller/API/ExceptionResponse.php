<?php

namespace App\Controller\API;

/**
 * API exception.
 */
class ExceptionResponse extends APIResponse
{
    protected int $code;

    protected string $message;

    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    protected function get_response_object(): object|array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
        ];
    }
}
