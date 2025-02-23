<?php

namespace App\Controller;

/**
 * Interface voor renderbare pagina controllers.
 */
interface Response
{
    public function render(): string;
}
