<?php
namespace Controller\API;

use JetBrains\PhpStorm\ArrayShape;

/**
 * API exception.
 */
class ExceptionResponse extends APIResponse
{
    /** @var int  */
    protected int $code;

    /** @var string  */
    protected string $message;

    /**
     * @param int $code
     * @param string $message
     */
    function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    /**
     * @return object|array
     */
    #[ArrayShape(['code' => "int", 'message' => "string"])] protected function get_response_object(): object|array
    {
        return [
            'code' => $this->code,
            'message' => $this->message
        ];
    }
}