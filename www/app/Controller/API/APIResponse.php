<?php

namespace App\Controller\API;

use App\Controller\Response;

/**
 * Basis voor elke API.
 */
abstract class APIResponse implements Response
{
    /**
     * @return string
     */
    public function render(): string
    {
        return json_encode($this->get_response_object());
    }

    /**
     * @return object|array
     */
    abstract protected function get_response_object(): object|array;
}
