<?php

namespace App\Controller\API;

use App\Controller\Response;

/**
 * Basis voor elke API.
 */
abstract class APIResponse implements Response
{
    public function render(): string
    {
        return json_encode($this->get_response_object());
    }

    abstract protected function get_response_object(): object|array;
}
